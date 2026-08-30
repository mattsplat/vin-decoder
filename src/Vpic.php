<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode;

use Mattsplat\VinDecode\DTO\CanadianSpecification;
use Mattsplat\VinDecode\DTO\Make;
use Mattsplat\VinDecode\DTO\Manufacturer;
use Mattsplat\VinDecode\DTO\ManufacturerDetail;
use Mattsplat\VinDecode\DTO\Model;
use Mattsplat\VinDecode\DTO\Part;
use Mattsplat\VinDecode\DTO\PlantCode;
use Mattsplat\VinDecode\DTO\Variable;
use Mattsplat\VinDecode\DTO\VariableValue;
use Mattsplat\VinDecode\DTO\VehicleType;
use Mattsplat\VinDecode\DTO\VinResult;
use Mattsplat\VinDecode\DTO\VinVariable;
use Mattsplat\VinDecode\DTO\Wmi;
use Mattsplat\VinDecode\DTO\WmiManufacturer;
use Mattsplat\VinDecode\Exceptions\VpicException;
use Mattsplat\VinDecode\Responses\Response;

/**
 * Client for the NHTSA vPIC vehicle API — one method per documented endpoint.
 *
 * @see https://vpic.nhtsa.dot.gov/api/
 */
class Vpic
{
    /**
     * The maximum number of VINs the batch decoder accepts in a single request.
     */
    public const BATCH_LIMIT = 50;

    private Client $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client();
    }

    /**
     * The underlying HTTP client.
     */
    public function client(): Client
    {
        return $this->client;
    }

    /* ---------------------------------------------------------------------
     |  VIN decoding
     | ------------------------------------------------------------------- */

    /**
     * Decode a VIN into variable/value rows (DecodeVin).
     *
     * @return Response<VinVariable>
     */
    public function decodeVin(string $vin, ?int $modelYear = null): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/DecodeVin/'.rawurlencode($vin), ['modelyear' => $modelYear]),
            VinVariable::fromArray(...),
        );
    }

    /**
     * Decode a VIN into a single flat object (DecodeVinValues).
     */
    public function decodeVinFlat(string $vin, ?int $modelYear = null): VinResult
    {
        $payload = $this->client->get(
            'vehicles/DecodeVinValues/'.rawurlencode($vin),
            ['modelyear' => $modelYear],
        );

        return VinResult::fromArray($this->firstRow($payload));
    }

    /**
     * Decode a VIN with the extended NCSA variables (DecodeVinExtended).
     *
     * @return Response<VinVariable>
     */
    public function decodeVinExtended(string $vin, ?int $modelYear = null): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/DecodeVinExtended/'.rawurlencode($vin), ['modelyear' => $modelYear]),
            VinVariable::fromArray(...),
        );
    }

    /**
     * Decode a VIN with the extended NCSA variables, flat (DecodeVinValuesExtended).
     */
    public function decodeVinFlatExtended(string $vin, ?int $modelYear = null): VinResult
    {
        $payload = $this->client->get(
            'vehicles/DecodeVinValuesExtended/'.rawurlencode($vin),
            ['modelyear' => $modelYear],
        );

        return VinResult::fromArray($this->firstRow($payload));
    }

    /**
     * Decode up to 50 VINs in one request (DecodeVinValuesBatch).
     *
     * Each entry is either a VIN string, or a `[vin, modelYear]` pair.
     *
     * @param  list<string|array{0: string, 1: int|string|null}>  $vins
     * @return Response<VinResult>
     */
    public function decodeVinBatch(array $vins): Response
    {
        if ($vins === []) {
            throw new VpicException('decodeVinBatch() requires at least one VIN.');
        }

        if (count($vins) > self::BATCH_LIMIT) {
            throw new VpicException(sprintf(
                'decodeVinBatch() accepts at most %d VINs, %d given.',
                self::BATCH_LIMIT,
                count($vins),
            ));
        }

        $entries = [];
        foreach ($vins as $entry) {
            if (is_array($entry)) {
                [$vin, $year] = [$entry[0], $entry[1] ?? null];
                $entries[] = $year === null ? $vin : $vin.','.$year;
            } else {
                $entries[] = $entry;
            }
        }

        return Response::fromPayload(
            $this->client->post('vehicles/DecodeVinValuesBatch/', ['data' => implode(';', $entries)]),
            VinResult::fromArray(...),
        );
    }

    /* ---------------------------------------------------------------------
     |  WMI
     | ------------------------------------------------------------------- */

    /**
     * Decode a World Manufacturer Identifier (DecodeWMI).
     *
     * The WMI is the first 3 characters of a VIN, or positions 1-3 + 12-14.
     */
    public function decodeWmi(string $wmi): Wmi
    {
        $payload = $this->client->get('vehicles/DecodeWMI/'.rawurlencode($wmi));

        return Wmi::fromArray($this->firstRow($payload));
    }

    /**
     * All WMI codes registered to a manufacturer (GetWMIsForManufacturer).
     *
     * @param  string|int  $manufacturer  name, partial name, or ID
     * @return Response<WmiManufacturer>
     */
    public function wmisForManufacturer(string|int $manufacturer, ?string $vehicleType = null): Response
    {
        return Response::fromPayload(
            $this->client->get(
                'vehicles/GetWMIsForManufacturer/'.rawurlencode((string) $manufacturer),
                ['vehicleType' => $vehicleType],
            ),
            WmiManufacturer::fromArray(...),
        );
    }

    /* ---------------------------------------------------------------------
     |  Manufacturers & makes
     | ------------------------------------------------------------------- */

    /**
     * All manufacturers in the vPIC dataset, paginated 100 per page (GetAllManufacturers).
     *
     * @param  string|null  $manufacturerType  e.g. "Intermediate", "Completed Vehicle Manufacturer"
     * @return Response<Manufacturer>
     */
    public function allManufacturers(?string $manufacturerType = null, int $page = 1): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/GetAllManufacturers', [
                'ManufacturerType' => $manufacturerType,
                'page' => $page,
            ]),
            Manufacturer::fromArray(...),
        );
    }

    /**
     * Full registration details for a manufacturer (GetManufacturerDetails).
     *
     * @param  string|int  $manufacturer  name, partial name, or ID
     * @return Response<ManufacturerDetail>
     */
    public function manufacturerDetails(string|int $manufacturer, int $page = 1): Response
    {
        return Response::fromPayload(
            $this->client->get(
                'vehicles/GetManufacturerDetails/'.rawurlencode((string) $manufacturer),
                ['page' => $page],
            ),
            ManufacturerDetail::fromArray(...),
        );
    }

    /**
     * All makes produced by a manufacturer (GetMakeForManufacturer).
     *
     * @param  string|int  $manufacturer  name, partial name, or ID
     * @return Response<Make>
     */
    public function makesForManufacturer(string|int $manufacturer): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/GetMakeForManufacturer/'.rawurlencode((string) $manufacturer)),
            Make::fromArray(...),
        );
    }

    /**
     * Makes produced by a manufacturer in a given year (GetMakesForManufacturerAndYear).
     *
     * @param  string|int  $manufacturer  name, partial name, or ID
     * @return Response<Make>
     */
    public function makesForManufacturerAndYear(string|int $manufacturer, int $year): Response
    {
        return Response::fromPayload(
            $this->client->get(
                'vehicles/GetMakesForManufacturerAndYear/'.rawurlencode((string) $manufacturer),
                ['year' => $year],
            ),
            Make::fromArray(...),
        );
    }

    /**
     * Every make in the vPIC dataset (GetAllMakes).
     *
     * @return Response<Make>
     */
    public function allMakes(): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/GetAllMakes'),
            Make::fromArray(...),
        );
    }

    /* ---------------------------------------------------------------------
     |  Models
     | ------------------------------------------------------------------- */

    /**
     * Models for a make name (GetModelsForMake).
     *
     * @return Response<Model>
     */
    public function modelsForMake(string $make): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/GetModelsForMake/'.rawurlencode($make)),
            Model::fromArray(...),
        );
    }

    /**
     * Models for a make ID (GetModelsForMakeId).
     *
     * @return Response<Model>
     */
    public function modelsForMakeId(int $makeId): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/GetModelsForMakeId/'.$makeId),
            Model::fromArray(...),
        );
    }

    /**
     * Models for a make name in a given year, optionally filtered by vehicle type
     * (GetModelsForMakeYear).
     *
     * @return Response<Model>
     */
    public function modelsForMakeYear(string $make, int $year, ?string $vehicleType = null): Response
    {
        $path = 'vehicles/GetModelsForMakeYear/make/'.rawurlencode($make).'/modelyear/'.$year;

        if ($vehicleType !== null) {
            $path .= '/vehicleType/'.rawurlencode($vehicleType);
        }

        return Response::fromPayload(
            $this->client->get($path),
            Model::fromArray(...),
        );
    }

    /**
     * Models for a make ID in a given year, optionally filtered by vehicle type
     * (GetModelsForMakeIdYear).
     *
     * @return Response<Model>
     */
    public function modelsForMakeIdYear(int $makeId, int $year, ?string $vehicleType = null): Response
    {
        $path = 'vehicles/GetModelsForMakeIdYear/makeId/'.$makeId.'/modelyear/'.$year;

        if ($vehicleType !== null) {
            $path .= '/vehicleType/'.rawurlencode($vehicleType);
        }

        return Response::fromPayload(
            $this->client->get($path),
            Model::fromArray(...),
        );
    }

    /* ---------------------------------------------------------------------
     |  Vehicle types
     | ------------------------------------------------------------------- */

    /**
     * All makes for a vehicle type name or partial name (GetMakesForVehicleType).
     *
     * @return Response<Make>
     */
    public function makesForVehicleType(string $vehicleType): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/GetMakesForVehicleType/'.rawurlencode($vehicleType)),
            Make::fromArray(...),
        );
    }

    /**
     * Vehicle types for a make name (GetVehicleTypesForMake).
     *
     * @return Response<VehicleType>
     */
    public function vehicleTypesForMake(string $make): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/GetVehicleTypesForMake/'.rawurlencode($make)),
            VehicleType::fromArray(...),
        );
    }

    /**
     * Vehicle types for a make ID (GetVehicleTypesForMakeId).
     *
     * @return Response<VehicleType>
     */
    public function vehicleTypesForMakeId(int $makeId): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/GetVehicleTypesForMakeId/'.$makeId),
            VehicleType::fromArray(...),
        );
    }

    /* ---------------------------------------------------------------------
     |  Variables, equipment & parts
     | ------------------------------------------------------------------- */

    /**
     * Every vehicle variable in the vPIC dataset (GetVehicleVariableList).
     *
     * @return Response<Variable>
     */
    public function vehicleVariableList(): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/GetVehicleVariableList'),
            Variable::fromArray(...),
        );
    }

    /**
     * Accepted values for a lookup variable, by name or ID (GetVehicleVariableValuesList).
     *
     * @return Response<VariableValue>
     */
    public function vehicleVariableValues(string|int $variable): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/GetVehicleVariableValuesList/'.rawurlencode((string) $variable)),
            VariableValue::fromArray(...),
        );
    }

    /**
     * Assigned equipment plant codes for a year (GetEquipmentPlantCodes).
     *
     * @param  int  $equipmentType  1 = tyres, 3 = brake hoses, 13 = glazing, 16 = retread
     * @param  string  $reportType  "New", "Updated", or "Closed"
     * @return Response<PlantCode>
     */
    public function equipmentPlantCodes(int $year, int $equipmentType, string $reportType = 'All'): Response
    {
        return Response::fromPayload(
            $this->client->get('vehicles/GetEquipmentPlantCodes', [
                'year' => $year,
                'equipmentType' => $equipmentType,
                'reportType' => $reportType,
            ]),
            PlantCode::fromArray(...),
        );
    }

    /**
     * ORG regulatory submissions within a date range (GetParts), 1000 per page.
     *
     * @param  int  $type  ORG type, e.g. 565 or 566
     * @param  string  $fromDate  m/d/Y
     * @param  string  $toDate  m/d/Y
     * @return Response<Part>
     */
    public function parts(
        int $type,
        string $fromDate,
        string $toDate,
        string|int|null $manufacturer = null,
        int $page = 1,
    ): Response {
        return Response::fromPayload(
            $this->client->get('vehicles/GetParts', [
                'type' => $type,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'manufacturer' => $manufacturer === null ? null : (string) $manufacturer,
                'page' => $page,
            ]),
            Part::fromArray(...),
        );
    }

    /* ---------------------------------------------------------------------
     |  Canadian specifications
     | ------------------------------------------------------------------- */

    /**
     * Transport Canada original vehicle dimensions (GetCanadianVehicleSpecifications).
     *
     * @param  string  $units  "Metric" (default) or "US"
     * @return Response<CanadianSpecification>
     */
    public function canadianVehicleSpecifications(
        int $year,
        ?string $make = null,
        ?string $model = null,
        string $units = 'Metric',
    ): Response {
        return Response::fromPayload(
            $this->client->get('vehicles/GetCanadianVehicleSpecifications/', [
                'year' => $year,
                'make' => $make,
                'model' => $model,
                'units' => $units,
            ]),
            CanadianSpecification::fromArray(...),
        );
    }

    /* ------------------------------------------------------------------- */

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function firstRow(array $payload): array
    {
        $rows = $payload['Results'] ?? [];

        if (is_array($rows) && isset($rows[0]) && is_array($rows[0])) {
            return $rows[0];
        }

        return [];
    }
}

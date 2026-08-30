<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * A vehicle make.
 *
 * Returned by GetAllMakes, GetMakeForManufacturer, GetMakesForManufacturerAndYear
 * and GetMakesForVehicleType. Depending on the endpoint the row may also carry a
 * manufacturer and/or a vehicle type.
 */
final readonly class Make
{
    public function __construct(
        public int $id,
        public string $name,
        public ?int $manufacturerId = null,
        public ?string $manufacturerName = null,
        public ?int $vehicleTypeId = null,
        public ?string $vehicleTypeName = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            id: Attr::int($row, ['Make_ID', 'MakeId', 'MakeID']),
            name: Attr::string($row, ['Make_Name', 'MakeName']),
            manufacturerId: Attr::nullableInt($row, ['MfrId', 'Mfr_ID', 'ManufacturerId']),
            manufacturerName: Attr::nullableString($row, ['Mfr_Name', 'MfrName', 'ManufacturerName']),
            vehicleTypeId: Attr::nullableInt($row, 'VehicleTypeId'),
            vehicleTypeName: Attr::nullableString($row, 'VehicleTypeName'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'manufacturerId' => $this->manufacturerId,
            'manufacturerName' => $this->manufacturerName,
            'vehicleTypeId' => $this->vehicleTypeId,
            'vehicleTypeName' => $this->vehicleTypeName,
        ];
    }
}

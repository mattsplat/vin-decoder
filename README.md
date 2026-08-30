# vin-decode

[![CI](https://github.com/mattsplat/vin-decoder/actions/workflows/ci.yml/badge.svg)](https://github.com/mattsplat/vin-decoder/actions/workflows/ci.yml)
[![Latest Version](https://img.shields.io/packagist/v/mattsplat/vin-decode.svg)](https://packagist.org/packages/mattsplat/vin-decode)
[![License](https://img.shields.io/packagist/l/mattsplat/vin-decode.svg)](LICENSE)

A PHP wrapper for the [National Highway Traffic Safety Administration's vPIC
API](https://vpic.nhtsa.dot.gov/api/) — decode VINs and look up vehicle makes,
models, manufacturers, WMI codes, equipment plant codes and more.

- One method per documented vPIC endpoint
- Typed value objects for every response
- Guzzle under the hood; inject your own client for testing or custom middleware
- No API key required (vPIC is free and unauthenticated)

## Install

```bash
composer require mattsplat/vin-decode
```

Requires PHP 8.1+.

## Quick start

```php
use Mattsplat\VinDecode\Vpic;

$vpic = new Vpic();

// Decode a VIN into one flat object
$vehicle = $vpic->decodeVinFlat('5UXWX7C5*BA', 2011);

$vehicle->make();            // "BMW"
$vehicle->model();           // "X3"
$vehicle->modelYear();       // 2011
$vehicle->bodyClass();       // "Sport Utility Vehicle (SUV)/Multipurpose Vehicle (MPV)"
$vehicle->engineCylinders(); // 6
$vehicle->get('PlantCity');  // "MUNICH" — any raw vPIC field

// List every model a make has registered
foreach ($vpic->modelsForMake('Honda') as $model) {
    echo $model->name, "\n";
}
```

List endpoints return a `Response` that is countable, iterable and array-accessible:

```php
$response = $vpic->allManufacturers(page: 2);

$response->count();          // rows in this response
$response->reportedCount;    // vPIC's own Count field
$response->message;          // vPIC status message
$response->first();          // first DTO, or null
$response->all();            // list<Manufacturer>
```

Errors (transport failure, non-2xx, non-JSON body) throw
`Mattsplat\VinDecode\Exceptions\VpicRequestException`, which extends
`Mattsplat\VinDecode\Exceptions\VpicException`.

## Method reference

<a name="method-reference"></a>

### VIN decoding

| Method | vPIC endpoint | Returns |
| --- | --- | --- |
| `decodeVin(string $vin, ?int $modelYear = null)` | `DecodeVin` | `Response<VinVariable>` |
| `decodeVinFlat(string $vin, ?int $modelYear = null)` | `DecodeVinValues` | `VinResult` |
| `decodeVinExtended(string $vin, ?int $modelYear = null)` | `DecodeVinExtended` | `Response<VinVariable>` |
| `decodeVinFlatExtended(string $vin, ?int $modelYear = null)` | `DecodeVinValuesExtended` | `VinResult` |
| `decodeVinBatch(array $vins)` | `DecodeVinValuesBatch` (POST, max 50) | `Response<VinResult>` |

### WMI

| Method | vPIC endpoint | Returns |
| --- | --- | --- |
| `decodeWmi(string $wmi)` | `DecodeWMI` | `Wmi` |
| `wmisForManufacturer(string\|int $manufacturer, ?string $vehicleType = null)` | `GetWMIsForManufacturer` | `Response<WmiManufacturer>` |

### Manufacturers & makes

| Method | vPIC endpoint | Returns |
| --- | --- | --- |
| `allManufacturers(?string $manufacturerType = null, int $page = 1)` | `GetAllManufacturers` | `Response<Manufacturer>` |
| `manufacturerDetails(string\|int $manufacturer, int $page = 1)` | `GetManufacturerDetails` | `Response<ManufacturerDetail>` |
| `makesForManufacturer(string\|int $manufacturer)` | `GetMakeForManufacturer` | `Response<Make>` |
| `makesForManufacturerAndYear(string\|int $manufacturer, int $year)` | `GetMakesForManufacturerAndYear` | `Response<Make>` |
| `allMakes()` | `GetAllMakes` | `Response<Make>` |

### Models

| Method | vPIC endpoint | Returns |
| --- | --- | --- |
| `modelsForMake(string $make)` | `GetModelsForMake` | `Response<Model>` |
| `modelsForMakeId(int $makeId)` | `GetModelsForMakeId` | `Response<Model>` |
| `modelsForMakeYear(string $make, int $year, ?string $vehicleType = null)` | `GetModelsForMakeYear` | `Response<Model>` |
| `modelsForMakeIdYear(int $makeId, int $year, ?string $vehicleType = null)` | `GetModelsForMakeIdYear` | `Response<Model>` |

### Vehicle types

| Method | vPIC endpoint | Returns |
| --- | --- | --- |
| `makesForVehicleType(string $vehicleType)` | `GetMakesForVehicleType` | `Response<Make>` |
| `vehicleTypesForMake(string $make)` | `GetVehicleTypesForMake` | `Response<VehicleType>` |
| `vehicleTypesForMakeId(int $makeId)` | `GetVehicleTypesForMakeId` | `Response<VehicleType>` |

### Variables, equipment & parts

| Method | vPIC endpoint | Returns |
| --- | --- | --- |
| `vehicleVariableList()` | `GetVehicleVariableList` | `Response<Variable>` |
| `vehicleVariableValues(string\|int $variable)` | `GetVehicleVariableValuesList` | `Response<VariableValue>` |
| `equipmentPlantCodes(int $year, int $equipmentType, string $reportType = 'All')` | `GetEquipmentPlantCodes` | `Response<PlantCode>` |
| `parts(int $type, string $fromDate, string $toDate, string\|int\|null $manufacturer = null, int $page = 1)` | `GetParts` | `Response<Part>` |

### Canadian vehicle specifications

| Method | vPIC endpoint | Returns |
| --- | --- | --- |
| `canadianVehicleSpecifications(int $year, ?string $make = null, ?string $model = null, string $units = 'Metric')` | `GetCanadianVehicleSpecifications` | `Response<CanadianSpecification>` |

## Documentation

Per-endpoint guides with request/response examples live in [`docs/`](docs/README.md).

## Custom HTTP client

```php
use GuzzleHttp\Client as GuzzleClient;
use Mattsplat\VinDecode\Client;
use Mattsplat\VinDecode\Vpic;

$vpic = new Vpic(new Client(new GuzzleClient([
    'timeout' => 5,
    // ... proxy, middleware, retry handler, etc.
])));
```

## Upgrading from 0.x

The original `VinDecode\VinDecode` class still works and is kept as a thin,
**deprecated** shim over `Vpic`:

```php
use VinDecode\VinDecode; // still works

$decode = new VinDecode();
$decode->setVIN('5UXWX7C5XBL41234');
$decode->searchVIN();     // snake_cased flat array, as before
echo $decode->make;
```

New code should use `Mattsplat\VinDecode\Vpic` directly. See [CHANGELOG.md](CHANGELOG.md).

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md). Run `composer test`, `composer lint` and
`composer analyse` before opening a PR.

## Security

See [SECURITY.md](SECURITY.md).

## License

MIT — see [LICENSE](LICENSE).

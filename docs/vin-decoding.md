# VIN decoding

| Method | Endpoint | Returns |
| --- | --- | --- |
| `decodeVin($vin, $modelYear = null)` | `DecodeVin` | `Response<VinVariable>` |
| `decodeVinFlat($vin, $modelYear = null)` | `DecodeVinValues` | `VinResult` |
| `decodeVinExtended($vin, $modelYear = null)` | `DecodeVinExtended` | `Response<VinVariable>` |
| `decodeVinFlatExtended($vin, $modelYear = null)` | `DecodeVinValuesExtended` | `VinResult` |
| `decodeVinBatch($vins)` | `DecodeVinValuesBatch` | `Response<VinResult>` |

Partial VINs are allowed — use `*` for unknown positions (e.g. `5UXWX7C5*BA`).
The "extended" variants add NCSA program variables used in crash data.

## `decodeVinFlat()` — one flat object

The most common call. Returns a `VinResult` wrapping the ~150-field flat row.

```php
$v = $vpic->decodeVinFlat('5UXWX7C5*BA', 2011);

$v->make();               // "BMW"
$v->model();              // "X3"
$v->modelYear();          // 2011  (int)
$v->manufacturer();       // "BMW MANUFACTURER CORPORATION / BMW NORTH AMERICA"
$v->vehicleType();        // "MULTIPURPOSE PASSENGER VEHICLE (MPV)"
$v->bodyClass();
$v->trim();
$v->series();
$v->doors();              // int|null
$v->engineCylinders();    // int|null
$v->displacementLitres(); // float|null
$v->engineHorsePower();   // float|null
$v->fuelTypePrimary();
$v->fuelTypeSecondary();
$v->electrificationLevel();
$v->driveType();
$v->transmissionStyle();
$v->plantCountry();
$v->plantCity();
$v->vehicleDescriptor();

$v->errorCode();          // "0" = clean decode; comma-separated codes otherwise
$v->errorText();
$v->isValid();            // true when errorCode is "0" or absent
```

Any field vPIC returns is reachable by its raw name — blank values come back as
`null`:

```php
$v->get('BrakeSystemType');      // null when vPIC has no data
$v->get('Seats', 'unknown');     // with a default
$v['BodyCabType'];               // array access, same as get()
$v->has('BatteryType');          // false
$v->toArray();                   // the full underlying row
```

## `decodeVin()` — variable/value rows

Returns one `VinVariable` per decoded attribute, mirroring vPIC's default shape.
Useful when you want vPIC's `VariableId` / `ValueId` codes.

```php
foreach ($vpic->decodeVin('5UXWX7C5*BA', 2011) as $row) {
    // $row->variable   "Make"
    // $row->value      "BMW"
    // $row->variableId 26
    // $row->valueId    "452"
}
```

## `decodeVinBatch()` — up to 50 VINs

One request, `POST` body. Each entry is a VIN string, or a `[vin, year]` pair.

```php
$response = $vpic->decodeVinBatch([
    ['5UXWX7C5*BA', 2011],
    '5YJSA3DS*EF',
]);

foreach ($response as $vinResult) {
    echo $vinResult->get('VIN'), ' => ', $vinResult->make(), "\n";
}
```

Passing more than 50 entries, or an empty array, throws `VpicException`.

## `VinResult` reference

| Accessor | vPIC field | Type |
| --- | --- | --- |
| `make()` | `Make` | `?string` |
| `model()` | `Model` | `?string` |
| `modelYear()` | `ModelYear` | `?int` |
| `manufacturer()` | `Manufacturer` | `?string` |
| `vehicleType()` | `VehicleType` | `?string` |
| `bodyClass()` | `BodyClass` | `?string` |
| `trim()` | `Trim` | `?string` |
| `series()` | `Series` | `?string` |
| `doors()` | `Doors` | `?int` |
| `engineCylinders()` | `EngineCylinders` | `?int` |
| `displacementLitres()` | `DisplacementL` | `?float` |
| `engineHorsePower()` | `EngineHP` | `?float` |
| `fuelTypePrimary()` | `FuelTypePrimary` | `?string` |
| `fuelTypeSecondary()` | `FuelTypeSecondary` | `?string` |
| `electrificationLevel()` | `ElectrificationLevel` | `?string` |
| `driveType()` | `DriveType` | `?string` |
| `transmissionStyle()` | `TransmissionStyle` | `?string` |
| `plantCountry()` | `PlantCountry` | `?string` |
| `plantCity()` | `PlantCity` | `?string` |
| `vin()` | `VIN` | `?string` |
| `vehicleDescriptor()` | `VehicleDescriptor` | `?string` |
| `errorCode()` | `ErrorCode` | `?string` |
| `errorText()` | `ErrorText` | `?string` |
| `get($key, $default = null)` | any | `mixed` |

# vin-decode documentation

A PHP wrapper for the [NHTSA vPIC API](https://vpic.nhtsa.dot.gov/api/).

## Guides

- [Getting started](getting-started.md) — install, construct the client, responses, pagination, errors
- [VIN decoding](vin-decoding.md) — `decodeVin`, flat / extended variants, batch, the `VinResult` object
- [Manufacturers & makes](manufacturers-makes.md)
- [Models](models.md)
- [WMI](wmi.md) — World Manufacturer Identifier lookups
- [Vehicle types](vehicle-types.md)
- [Variables, equipment & parts](variables-equipment-parts.md)
- [Canadian vehicle specifications](canadian-specifications.md)

## Method reference

The full method → endpoint → return-type table is in the
[project README](../README.md#method-reference).

## DTOs

All response objects live in `Mattsplat\VinDecode\DTO` and are immutable. List
endpoints return `Mattsplat\VinDecode\Responses\Response`, which is iterable,
countable and array-accessible over the DTO rows.

| DTO | Produced by |
| --- | --- |
| `VinResult` | `decodeVinFlat`, `decodeVinFlatExtended`, `decodeVinBatch` |
| `VinVariable` | `decodeVin`, `decodeVinExtended` |
| `Make` | `allMakes`, `makesForManufacturer`, `makesForManufacturerAndYear`, `makesForVehicleType` |
| `Model` | `modelsForMake`, `modelsForMakeId`, `modelsForMakeYear`, `modelsForMakeIdYear` |
| `Manufacturer` | `allManufacturers` |
| `ManufacturerDetail` | `manufacturerDetails` |
| `ManufacturerVehicleType` | nested in `Manufacturer` / `ManufacturerDetail` |
| `Wmi` | `decodeWmi` |
| `WmiManufacturer` | `wmisForManufacturer` |
| `VehicleType` | `vehicleTypesForMake`, `vehicleTypesForMakeId` |
| `Variable` | `vehicleVariableList` |
| `VariableValue` | `vehicleVariableValues` |
| `PlantCode` | `equipmentPlantCodes` |
| `Part` | `parts` |
| `CanadianSpecification` | `canadianVehicleSpecifications` |

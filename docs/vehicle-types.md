# Vehicle types

| Method | Endpoint | Returns |
| --- | --- | --- |
| `makesForVehicleType($vehicleType)` | `GetMakesForVehicleType` | `Response<Make>` |
| `vehicleTypesForMake($make)` | `GetVehicleTypesForMake` | `Response<VehicleType>` |
| `vehicleTypesForMakeId($makeId)` | `GetVehicleTypesForMakeId` | `Response<VehicleType>` |

`$vehicleType` is a name or partial name: `car`, `truck`, `mpv`, `motorcycle`,
`bus`, `trailer`, ...

## `makesForVehicleType()`

```php
foreach ($vpic->makesForVehicleType('car') as $make) {
    // $make->id                441
    // $make->name              "TESLA"
    // $make->vehicleTypeId     2
    // $make->vehicleTypeName   "Passenger Car"
}
```

## `vehicleTypesForMake()` / `vehicleTypesForMakeId()`

```php
foreach ($vpic->vehicleTypesForMake('mercedes') as $type) {
    // $type->id        2
    // $type->name      "Passenger Car"
    // $type->makeId    449          (null when looked up by ID)
    // $type->makeName  "MERCEDES-BENZ"
}

$vpic->vehicleTypesForMakeId(449);   // $type->makeId / makeName are null
```

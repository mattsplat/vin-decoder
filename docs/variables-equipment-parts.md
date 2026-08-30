# Variables, equipment & parts

| Method | Endpoint | Returns |
| --- | --- | --- |
| `vehicleVariableList()` | `GetVehicleVariableList` | `Response<Variable>` |
| `vehicleVariableValues($variable)` | `GetVehicleVariableValuesList` | `Response<VariableValue>` |
| `equipmentPlantCodes($year, $equipmentType, $reportType = 'All')` | `GetEquipmentPlantCodes` | `Response<PlantCode>` |
| `parts($type, $fromDate, $toDate, $manufacturer = null, $page = 1)` | `GetParts` | `Response<Part>` |

## Vehicle variables

`vehicleVariableList()` returns every variable vPIC can decode. A variable whose
`dataType` is `lookup` has a fixed set of accepted values.

```php
foreach ($vpic->vehicleVariableList() as $variable) {
    // $variable->id           2
    // $variable->name         "Battery Type"
    // $variable->dataType     "lookup"
    // $variable->groupName    "Mechanical/Battery"
    // $variable->description  "<p>Battery type field stores ...</p>"
    // $variable->isLookup()   true
}
```

Fetch the accepted values for a lookup variable (by name or ID):

```php
foreach ($vpic->vehicleVariableValues('battery type') as $value) {
    // $value->id           3
    // $value->name         "Lithium-Ion/Li-Ion"
    // $value->elementName  "Battery Type"
}
```

## Equipment plant codes

```php
$codes = $vpic->equipmentPlantCodes(2015, equipmentType: 1, reportType: 'New');

foreach ($codes as $plant) {
    // $plant->dotCode        "00T"
    // $plant->oldDotCode
    // $plant->name           "Hankook Tire Manufacturing Tennessee, LP"
    // $plant->address / city / stateProvince / country / postalCode
    // $plant->status
}
```

`equipmentType`: `1` tyres, `3` brake hoses, `13` glazing, `16` retread.
`reportType`: `New`, `Updated`, `Closed` (or `All`).

## Parts (ORG regulatory submissions)

Manufacturer submissions of a given ORG type within a date range. Paginated,
up to 1000 rows per page. Dates are `m/d/Y` strings.

```php
$parts = $vpic->parts(565, '1/1/2015', '5/1/2015', page: 1);

foreach ($parts as $part) {
    // $part->name             "ORG10655"
    // $part->type             "565"
    // $part->letterDate       "4/30/2015"
    // $part->manufacturerId   987
    // $part->manufacturerName "HONDA MOTOR CO., LTD."
    // $part->modelYearFrom / modelYearTo   (int|null)
    // $part->url
    // $part->coverLetterUrl
}

// Optionally scope to one manufacturer (name, partial, or ID):
$vpic->parts(565, '1/1/2015', '5/1/2015', manufacturer: 'honda');
```

# WMI (World Manufacturer Identifier)

| Method | Endpoint | Returns |
| --- | --- | --- |
| `decodeWmi($wmi)` | `DecodeWMI` | `Wmi` |
| `wmisForManufacturer($manufacturer, $vehicleType = null)` | `GetWMIsForManufacturer` | `Response<WmiManufacturer>` |

The WMI is the first 3 characters of a VIN (for low-volume manufacturers,
positions 1–3 combined with 12–14).

## `decodeWmi()`

Returns a single `Wmi`:

```php
$wmi = $vpic->decodeWmi('1FD');

$wmi->make;                  // "FORD"
$wmi->manufacturerName;      // "FORD MOTOR COMPANY"
$wmi->commonName;            // "Ford"
$wmi->parentCompanyName;
$wmi->vehicleType;           // "Incomplete Vehicle"
$wmi->url;                   // "http://www.ford.com/"
$wmi->createdOn;
$wmi->updatedOn;
$wmi->dateAvailableToPublic;
```

## `wmisForManufacturer()`

Every WMI registered to a manufacturer (name, partial name, or ID). Optionally
filter by vehicle type.

```php
foreach ($vpic->wmisForManufacturer('hon', 'Motorcycle') as $entry) {
    // $entry->wmi                 "JH2"
    // $entry->manufacturerId      987
    // $entry->manufacturerName    "HONDA MOTOR CO., LTD."
    // $entry->country             "JAPAN"
    // $entry->vehicleType         "Motorcycle"
}
```

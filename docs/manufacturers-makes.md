# Manufacturers & makes

| Method | Endpoint | Returns |
| --- | --- | --- |
| `allManufacturers($manufacturerType = null, $page = 1)` | `GetAllManufacturers` | `Response<Manufacturer>` |
| `manufacturerDetails($manufacturer, $page = 1)` | `GetManufacturerDetails` | `Response<ManufacturerDetail>` |
| `makesForManufacturer($manufacturer)` | `GetMakeForManufacturer` | `Response<Make>` |
| `makesForManufacturerAndYear($manufacturer, $year)` | `GetMakesForManufacturerAndYear` | `Response<Make>` |
| `allMakes()` | `GetAllMakes` | `Response<Make>` |

`$manufacturer` accepts a full name, a partial name, or a numeric manufacturer ID.

## `allManufacturers()`

Paginated, 100 per page.

```php
$page = $vpic->allManufacturers(page: 2);

foreach ($page as $mfr) {
    // $mfr->id          957
    // $mfr->name        "BMW OF NORTH AMERICA, LLC"
    // $mfr->commonName  "BMW"
    // $mfr->country     "UNITED STATES (USA)"
    // $mfr->vehicleTypes  list<ManufacturerVehicleType>{ name, isPrimary }
}
```

Filter by manufacturer type:

```php
$vpic->allManufacturers('Completed Vehicle Manufacturer');
```

## `manufacturerDetails()`

The full registration record.

```php
$detail = $vpic->manufacturerDetails(987)->first();

$detail->id;                 // 987
$detail->name;               // "HONDA MOTOR CO., LTD."
$detail->commonName;
$detail->country;
$detail->address;
$detail->city;
$detail->stateProvince;
$detail->postalCode;
$detail->contactEmail;
$detail->contactPhone;
$detail->primaryProduct;
$detail->principalFirstName;
$detail->principalLastName;
$detail->manufacturerTypes;  // list<string>
$detail->vehicleTypes;       // list<ManufacturerVehicleType>
$detail->get('SubmittedOn'); // any raw field
```

## `makesForManufacturer()` / `makesForManufacturerAndYear()`

```php
foreach ($vpic->makesForManufacturer('honda') as $make) {
    // $make->id                474
    // $make->name              "HONDA"
    // $make->manufacturerName  "HONDA MOTOR CO., LTD."
}

$vpic->makesForManufacturerAndYear('honda', 2020); // adds $make->manufacturerId
```

## `allMakes()`

Every make in the dataset (12,000+). `Make` here only has `id` and `name`.

```php
$makes = $vpic->allMakes();          // $makes->reportedCount ~= 12351
$makes->first()->name;
```

# Models

| Method | Endpoint | Returns |
| --- | --- | --- |
| `modelsForMake($make)` | `GetModelsForMake` | `Response<Model>` |
| `modelsForMakeId($makeId)` | `GetModelsForMakeId` | `Response<Model>` |
| `modelsForMakeYear($make, $year, $vehicleType = null)` | `GetModelsForMakeYear` | `Response<Model>` |
| `modelsForMakeIdYear($makeId, $year, $vehicleType = null)` | `GetModelsForMakeIdYear` | `Response<Model>` |

Every row maps to a `Model`:

```php
foreach ($vpic->modelsForMake('Honda') as $model) {
    // $model->id        1861
    // $model->name      "Accord"
    // $model->makeId    474
    // $model->makeName  "Honda"
}
```

## By make ID

```php
$vpic->modelsForMakeId(474);
```

## Filtered by year and vehicle type

`$vehicleType` is a name or partial name ("car", "truck", "mpv", ...). When
given it is appended to the URL path as vPIC expects.

```php
$vpic->modelsForMakeYear('honda', 2015);
$vpic->modelsForMakeYear('honda', 2015, 'truck');
$vpic->modelsForMakeIdYear(474, 2015, 'truck');
```

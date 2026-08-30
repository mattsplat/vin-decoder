# Canadian vehicle specifications

| Method | Endpoint | Returns |
| --- | --- | --- |
| `canadianVehicleSpecifications($year, $make = null, $model = null, $units = 'Metric')` | `GetCanadianVehicleSpecifications` | `Response<CanadianSpecification>` |

Transport Canada's database of original vehicle dimensions. `$year` is required
(1971 onwards); `$make` and `$model` narrow the result. `$units` is `Metric`
(default) or `US`.

```php
$response = $vpic->canadianVehicleSpecifications(2011, make: 'Acura');

foreach ($response as $spec) {
    $spec->make();          // "ACURA"
    $spec->model();         // "CSX"
    $spec->modelYear();     // 2011
    $spec->get('Wheelbase');
    $spec->get('OverallLength');
    $spec->toArray();       // full name => value map
}
```

## Response shape

vPIC returns each row as a nested `Specs` array of `{Name, Value}` pairs.
`CanadianSpecification` flattens that into a plain map, read via `get()`:

```php
$spec->get('CurbWeight');            // "1364"
$spec->get('Missing', 'n/a');        // default for absent/blank
```

## Availability

This endpoint depends on a separate Transport Canada database that is
intermittently offline. When it is down vPIC responds with HTTP 200, `Count: 0`,
an empty `Results` array, and a SQL error string in `Message` — this package
returns an **empty `Response`**, it does not throw. Check `$response->isEmpty()`
and `$response->message`.

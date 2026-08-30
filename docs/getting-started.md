# Getting started

## Install

```bash
composer require mattsplat/vin-decode
```

Requires PHP 8.1+ and `guzzlehttp/guzzle` ^7.5 (pulled in automatically).

## Construct the client

```php
use Mattsplat\VinDecode\Vpic;

$vpic = new Vpic();
```

vPIC needs no API key. Every request is sent with `format=json` — you never pass
a format yourself.

### Custom Guzzle client

Inject your own `Mattsplat\VinDecode\Client` to control timeouts, proxies, retry
middleware or logging:

```php
use GuzzleHttp\Client as GuzzleClient;
use Mattsplat\VinDecode\Client;
use Mattsplat\VinDecode\Vpic;

$vpic = new Vpic(new Client(new GuzzleClient([
    'timeout' => 5,
])));
```

You can also point the client at a different base URI (useful for a proxy or a
recorded-response server):

```php
new Client(new GuzzleClient(), 'https://my-vpic-proxy.example/api/');
```

## Responses

### Single-object endpoints

`decodeVinFlat()`, `decodeVinFlatExtended()` and `decodeWmi()` return a single
DTO directly.

### List endpoints

Everything else returns `Mattsplat\VinDecode\Responses\Response`, which wraps the
vPIC `{Count, Message, SearchCriteria, Results}` envelope:

```php
$response = $vpic->allMakes();

$response->all();          // list<Make> — the mapped rows
$response->first();        // first Make, or null when empty
$response->count();        // number of rows in THIS response
$response->reportedCount;  // vPIC's own "Count" field
$response->message;        // vPIC status message
$response->searchCriteria; // vPIC "SearchCriteria" string, or null
$response->raw;            // the untouched decoded payload
$response->isEmpty();

foreach ($response as $make) { /* ... */ }   // iterable
$response[0];                                  // array-accessible (read-only)
count($response);                              // Countable
```

## Pagination

`allManufacturers()`, `manufacturerDetails()` and `parts()` are paginated by
vPIC. Pass `page` (1-based). `GetAllManufacturers` returns 100 rows per page;
`GetParts` up to 1000.

```php
$page1 = $vpic->allManufacturers(page: 1);
$page2 = $vpic->allManufacturers(page: 2);
```

## Model year

The VIN decoders accept an optional model year. vPIC recommends supplying it —
some vehicles can only be fully decoded with it, and it disambiguates the
30-year VIN year cycle.

```php
$vpic->decodeVinFlat('1FTFW1E50MFA00000', 2021);
```

## Error handling

Transport failures, non-2xx responses and non-JSON bodies throw
`Mattsplat\VinDecode\Exceptions\VpicRequestException`:

```php
use Mattsplat\VinDecode\Exceptions\VpicException;
use Mattsplat\VinDecode\Exceptions\VpicRequestException;

try {
    $vpic->decodeVinFlat('...');
} catch (VpicRequestException $e) {
    $e->endpoint();          // "vehicles/DecodeVinValues/..."
    $e->getPrevious();       // the underlying GuzzleException, if any
}
```

`VpicRequestException extends VpicException extends \RuntimeException`, so catch
`VpicException` to handle every error this package raises.

Note that vPIC itself returns HTTP 200 with an explanatory `Message` for many
"no data" cases (bad VIN, unknown make, the Canadian dataset being offline).
Those are **not** exceptions — inspect the returned DTO / `Response`. For VIN
decodes, `VinResult::isValid()` and `VinResult::errorCode()` report vPIC's
decode-level errors.

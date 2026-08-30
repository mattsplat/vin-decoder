<?php

namespace VinDecode;

use Mattsplat\VinDecode\Vpic;

/**
 * Backwards-compatible shim for the original 0.x API.
 *
 * @deprecated Use {@see \Mattsplat\VinDecode\Vpic} instead. This class is kept so that
 *             existing `use VinDecode\VinDecode;` code keeps working and will be
 *             removed in a future major release.
 */
class VinDecode
{
    public string $base_url = 'https://vpic.nhtsa.dot.gov/api/';

    public ?string $vin = null;

    /** @var array<string, string> */
    public array $results = [];

    public ?string $make = null;

    public ?string $model = null;

    public ?string $year = null;

    private Vpic $vpic;

    public function __construct(?Vpic $vpic = null)
    {
        $this->vpic = $vpic ?? new Vpic();
    }

    /**
     * @return bool false when the VIN is not 17 characters
     */
    public function setVIN(string $vin)
    {
        if (strlen($vin) !== 17) {
            return false;
        }

        $this->vin = $vin;

        return true;
    }

    /**
     * @param  string  $format  retained for signature compatibility; ignored (always JSON)
     * @return array<string, string>
     */
    public function searchVIN(string $format = 'json')
    {
        $decoded = $this->vpic->decodeVin((string) $this->vin);

        $results = [];
        foreach ($decoded as $variable) {
            if ($variable->value !== null && $variable->value !== '') {
                $results[$this->toSnakeCase($variable->variable)] = $variable->value;
            }
        }

        $this->setResults($results);

        return $this->results = $results;
    }

    /**
     * @param  string  $string
     * @return string
     */
    public function toSnakeCase($string)
    {
        return str_replace(' ', '_', strtolower($string));
    }

    /**
     * @param  array<string, string>  $results
     */
    protected function setResults($results): void
    {
        $this->make = $results['make'] ?? null;
        $this->model = $results['model'] ?? null;
        $this->year = $results['model_year'] ?? null;
    }
}

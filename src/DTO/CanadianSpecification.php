<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * A row from GetCanadianVehicleSpecifications.
 *
 * vPIC returns each row as a nested `Specs` array of `{Name, Value}` pairs; this
 * DTO flattens that into a plain name => value map exposed via {@see self::get()}.
 */
final readonly class CanadianSpecification
{
    /**
     * @param  array<string, string>  $specs
     */
    public function __construct(
        public array $specs,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        $specs = [];

        foreach ((array) ($row['Specs'] ?? []) as $pair) {
            if (is_array($pair) && isset($pair['Name'])) {
                $specs[(string) $pair['Name']] = trim((string) ($pair['Value'] ?? ''));
            }
        }

        return new self($specs);
    }

    public function get(string $name, ?string $default = null): ?string
    {
        $value = $this->specs[$name] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }

    public function make(): ?string
    {
        return $this->get('Make');
    }

    public function model(): ?string
    {
        return $this->get('Model');
    }

    public function modelYear(): ?int
    {
        $year = $this->get('myear') ?? $this->get('ModelYear');

        return $year === null ? null : (int) $year;
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return $this->specs;
    }
}

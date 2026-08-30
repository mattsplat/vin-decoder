<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

use ArrayAccess;
use Mattsplat\VinDecode\Exceptions\VpicException;

/**
 * A fully decoded VIN, as returned by DecodeVinValues / DecodeVinValuesExtended
 * and the batch decoder (one flat object per VIN).
 *
 * vPIC returns ~150 fields, most of them empty for any given vehicle and the set
 * changes over time, so rather than freeze them into constructor arguments this
 * DTO wraps the raw row. The common fields have typed accessors; everything else
 * is reachable via {@see self::get()} or array access (`$result['BodyClass']`).
 *
 * Empty-string values are normalised to null.
 *
 * @implements ArrayAccess<string, string|null>
 */
final readonly class VinResult implements ArrayAccess
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function __construct(
        public array $attributes,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self($row);
    }

    /**
     * Read a field by its vPIC name (e.g. "BodyClass", "EngineCylinders").
     * Returns $default when the field is absent or blank.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->attributes[$key] ?? null;

        if ($value === null || $value === '') {
            return $default;
        }

        return is_string($value) ? trim($value) : $value;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
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
        $year = $this->get('ModelYear');

        return $year === null ? null : (int) $year;
    }

    public function manufacturer(): ?string
    {
        return $this->get('Manufacturer');
    }

    public function vehicleType(): ?string
    {
        return $this->get('VehicleType');
    }

    public function bodyClass(): ?string
    {
        return $this->get('BodyClass');
    }

    public function trim(): ?string
    {
        return $this->get('Trim');
    }

    public function series(): ?string
    {
        return $this->get('Series');
    }

    public function doors(): ?int
    {
        $doors = $this->get('Doors');

        return $doors === null ? null : (int) $doors;
    }

    public function engineCylinders(): ?int
    {
        $cylinders = $this->get('EngineCylinders');

        return $cylinders === null ? null : (int) $cylinders;
    }

    public function displacementLitres(): ?float
    {
        $litres = $this->get('DisplacementL');

        return $litres === null ? null : (float) $litres;
    }

    public function engineHorsePower(): ?float
    {
        $hp = $this->get('EngineHP');

        return $hp === null ? null : (float) $hp;
    }

    public function fuelTypePrimary(): ?string
    {
        return $this->get('FuelTypePrimary');
    }

    public function fuelTypeSecondary(): ?string
    {
        return $this->get('FuelTypeSecondary');
    }

    public function electrificationLevel(): ?string
    {
        return $this->get('ElectrificationLevel');
    }

    public function driveType(): ?string
    {
        return $this->get('DriveType');
    }

    public function transmissionStyle(): ?string
    {
        return $this->get('TransmissionStyle');
    }

    public function plantCountry(): ?string
    {
        return $this->get('PlantCountry');
    }

    public function plantCity(): ?string
    {
        return $this->get('PlantCity');
    }

    public function vin(): ?string
    {
        return $this->get('VIN');
    }

    public function vehicleDescriptor(): ?string
    {
        return $this->get('VehicleDescriptor');
    }

    /**
     * vPIC error code(s) for this decode. "0" means a clean decode; multiple
     * codes are comma-separated.
     */
    public function errorCode(): ?string
    {
        return $this->get('ErrorCode');
    }

    public function errorText(): ?string
    {
        return $this->get('ErrorText');
    }

    /**
     * True when vPIC reported no decode errors (ErrorCode "0").
     */
    public function isValid(): bool
    {
        $code = $this->errorCode();

        return $code === null || $code === '0';
    }

    /**
     * The full underlying row, unmodified.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has((string) $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get((string) $offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new VpicException('VinResult is immutable.');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new VpicException('VinResult is immutable.');
    }
}

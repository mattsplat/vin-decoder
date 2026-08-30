<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * A vehicle-type entry nested inside a Manufacturer / ManufacturerDetail row.
 */
final readonly class ManufacturerVehicleType
{
    public function __construct(
        public string $name,
        public bool $isPrimary,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            name: Attr::string($row, 'Name'),
            isPrimary: Attr::bool($row, 'IsPrimary') ?? false,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['name' => $this->name, 'isPrimary' => $this->isPrimary];
    }
}

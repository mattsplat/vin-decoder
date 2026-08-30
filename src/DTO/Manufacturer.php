<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * A manufacturer summary, as returned by GetAllManufacturers.
 *
 * For the full registration record use ManufacturerDetail.
 */
final readonly class Manufacturer
{
    /**
     * @param  list<ManufacturerVehicleType>  $vehicleTypes
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $commonName,
        public ?string $country,
        public array $vehicleTypes,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        $types = [];
        $rawTypes = $row['VehicleTypes'] ?? [];
        if (is_array($rawTypes)) {
            foreach ($rawTypes as $type) {
                if (is_array($type)) {
                    $types[] = ManufacturerVehicleType::fromArray($type);
                }
            }
        }

        return new self(
            id: Attr::int($row, ['Mfr_ID', 'MfrId']),
            name: Attr::string($row, ['Mfr_Name', 'MfrName']),
            commonName: Attr::nullableString($row, 'Mfr_CommonName'),
            country: Attr::nullableString($row, 'Country'),
            vehicleTypes: $types,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'commonName' => $this->commonName,
            'country' => $this->country,
            'vehicleTypes' => array_map(
                static fn (ManufacturerVehicleType $t): array => $t->toArray(),
                $this->vehicleTypes,
            ),
        ];
    }
}

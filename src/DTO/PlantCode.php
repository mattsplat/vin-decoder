<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * An assigned equipment plant code, as returned by GetEquipmentPlantCodes.
 */
final readonly class PlantCode
{
    public function __construct(
        public string $dotCode,
        public ?string $oldDotCode,
        public string $name,
        public ?string $address,
        public ?string $city,
        public ?string $stateProvince,
        public ?string $country,
        public ?string $postalCode,
        public ?string $status,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            dotCode: Attr::string($row, 'DOTCode'),
            oldDotCode: Attr::nullableString($row, 'OldDotCode'),
            name: Attr::string($row, 'Name'),
            address: Attr::nullableString($row, 'Address'),
            city: Attr::nullableString($row, 'City'),
            stateProvince: Attr::nullableString($row, 'StateProvince'),
            country: Attr::nullableString($row, 'Country'),
            postalCode: Attr::nullableString($row, 'PostalCode'),
            status: Attr::nullableString($row, 'Status'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'dotCode' => $this->dotCode,
            'oldDotCode' => $this->oldDotCode,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'stateProvince' => $this->stateProvince,
            'country' => $this->country,
            'postalCode' => $this->postalCode,
            'status' => $this->status,
        ];
    }
}

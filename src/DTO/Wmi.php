<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * The decoded details for a World Manufacturer Identifier, as returned by DecodeWMI.
 */
final readonly class Wmi
{
    public function __construct(
        public ?string $make,
        public ?string $manufacturerName,
        public ?string $commonName,
        public ?string $parentCompanyName,
        public ?string $vehicleType,
        public ?string $url,
        public ?string $createdOn,
        public ?string $updatedOn,
        public ?string $dateAvailableToPublic,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            make: Attr::nullableString($row, 'Make'),
            manufacturerName: Attr::nullableString($row, 'ManufacturerName'),
            commonName: Attr::nullableString($row, 'CommonName'),
            parentCompanyName: Attr::nullableString($row, 'ParentCompanyName'),
            vehicleType: Attr::nullableString($row, 'VehicleType'),
            url: Attr::nullableString($row, 'URL'),
            createdOn: Attr::nullableString($row, 'CreatedOn'),
            updatedOn: Attr::nullableString($row, 'UpdatedOn'),
            dateAvailableToPublic: Attr::nullableString($row, 'DateAvailableToPublic'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'make' => $this->make,
            'manufacturerName' => $this->manufacturerName,
            'commonName' => $this->commonName,
            'parentCompanyName' => $this->parentCompanyName,
            'vehicleType' => $this->vehicleType,
            'url' => $this->url,
            'createdOn' => $this->createdOn,
            'updatedOn' => $this->updatedOn,
            'dateAvailableToPublic' => $this->dateAvailableToPublic,
        ];
    }
}

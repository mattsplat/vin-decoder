<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * A WMI code registered to a manufacturer, as returned by GetWMIsForManufacturer.
 */
final readonly class WmiManufacturer
{
    public function __construct(
        public string $wmi,
        public int $manufacturerId,
        public string $manufacturerName,
        public ?string $country,
        public ?string $vehicleType,
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
            wmi: Attr::string($row, 'WMI'),
            manufacturerId: Attr::int($row, 'Id'),
            manufacturerName: Attr::string($row, 'Name'),
            country: Attr::nullableString($row, 'Country'),
            vehicleType: Attr::nullableString($row, 'VehicleType'),
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
            'wmi' => $this->wmi,
            'manufacturerId' => $this->manufacturerId,
            'manufacturerName' => $this->manufacturerName,
            'country' => $this->country,
            'vehicleType' => $this->vehicleType,
            'createdOn' => $this->createdOn,
            'updatedOn' => $this->updatedOn,
            'dateAvailableToPublic' => $this->dateAvailableToPublic,
        ];
    }
}

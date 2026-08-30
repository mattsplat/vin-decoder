<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * A manufacturer's full registration record, as returned by GetManufacturerDetails.
 *
 * The most useful fields are exposed as typed properties; the complete row is
 * available via {@see self::$raw} and {@see self::get()}.
 */
final readonly class ManufacturerDetail
{
    /**
     * @param  list<string>  $manufacturerTypes
     * @param  list<ManufacturerVehicleType>  $vehicleTypes
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $commonName,
        public ?string $country,
        public ?string $address,
        public ?string $address2,
        public ?string $city,
        public ?string $stateProvince,
        public ?string $postalCode,
        public ?string $contactEmail,
        public ?string $contactPhone,
        public ?string $contactFax,
        public ?string $primaryProduct,
        public ?string $principalFirstName,
        public ?string $principalLastName,
        public ?string $dbas,
        public ?string $lastUpdated,
        public ?string $otherDetails,
        public array $manufacturerTypes,
        public array $vehicleTypes,
        public array $raw,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        $mfrTypes = [];
        foreach ((array) ($row['ManufacturerTypes'] ?? []) as $type) {
            if (is_array($type) && isset($type['Name'])) {
                $mfrTypes[] = (string) $type['Name'];
            }
        }

        $vehicleTypes = [];
        foreach ((array) ($row['VehicleTypes'] ?? []) as $type) {
            if (is_array($type)) {
                $vehicleTypes[] = ManufacturerVehicleType::fromArray($type);
            }
        }

        return new self(
            id: Attr::int($row, ['Mfr_ID', 'MfrId']),
            name: Attr::string($row, ['Mfr_Name', 'MfrName']),
            commonName: Attr::nullableString($row, 'Mfr_CommonName'),
            country: Attr::nullableString($row, 'Country'),
            address: Attr::nullableString($row, 'Address'),
            address2: Attr::nullableString($row, 'Address2'),
            city: Attr::nullableString($row, 'City'),
            stateProvince: Attr::nullableString($row, 'StateProvince'),
            postalCode: Attr::nullableString($row, 'PostalCode'),
            contactEmail: Attr::nullableString($row, 'ContactEmail'),
            contactPhone: Attr::nullableString($row, 'ContactPhone'),
            contactFax: Attr::nullableString($row, 'ContactFax'),
            primaryProduct: Attr::nullableString($row, 'PrimaryProduct'),
            principalFirstName: Attr::nullableString($row, 'PrincipalFirstName'),
            principalLastName: Attr::nullableString($row, 'PrincipalLastName'),
            dbas: Attr::nullableString($row, 'DBAs'),
            lastUpdated: Attr::nullableString($row, 'LastUpdated'),
            otherDetails: Attr::nullableString($row, 'OtherManufacturerDetails'),
            manufacturerTypes: $mfrTypes,
            vehicleTypes: $vehicleTypes,
            raw: $row,
        );
    }

    /**
     * Read any field straight from the underlying vPIC row.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->raw[$key] ?? $default;
    }
}

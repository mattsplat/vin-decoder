<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * A vehicle type, as returned by GetVehicleTypesForMake / GetVehicleTypesForMakeId.
 *
 * The make fields are only populated when the lookup was by make name.
 */
final readonly class VehicleType
{
    public function __construct(
        public int $id,
        public string $name,
        public ?int $makeId = null,
        public ?string $makeName = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            id: Attr::int($row, 'VehicleTypeId'),
            name: Attr::string($row, 'VehicleTypeName'),
            makeId: Attr::nullableInt($row, 'MakeId'),
            makeName: Attr::nullableString($row, 'MakeName'),
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
            'makeId' => $this->makeId,
            'makeName' => $this->makeName,
        ];
    }
}

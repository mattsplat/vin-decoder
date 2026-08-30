<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * A vehicle variable definition, as returned by GetVehicleVariableList.
 *
 * `dataType` of `lookup` means the accepted values can be fetched with
 * GetVehicleVariableValuesList.
 */
final readonly class Variable
{
    public function __construct(
        public int $id,
        public string $name,
        public string $dataType,
        public ?string $description,
        public ?string $groupName,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            id: Attr::int($row, 'ID'),
            name: Attr::string($row, 'Name'),
            dataType: Attr::string($row, 'DataType'),
            description: Attr::nullableString($row, 'Description'),
            groupName: Attr::nullableString($row, 'GroupName'),
        );
    }

    public function isLookup(): bool
    {
        return strtolower($this->dataType) === 'lookup';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dataType' => $this->dataType,
            'description' => $this->description,
            'groupName' => $this->groupName,
        ];
    }
}

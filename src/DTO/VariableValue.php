<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * An accepted value for a lookup variable, as returned by GetVehicleVariableValuesList.
 */
final readonly class VariableValue
{
    public function __construct(
        public int $id,
        public string $name,
        public string $elementName,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            id: Attr::int($row, 'Id'),
            name: Attr::string($row, 'Name'),
            elementName: Attr::string($row, 'ElementName'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return ['id' => $this->id, 'name' => $this->name, 'elementName' => $this->elementName];
    }
}

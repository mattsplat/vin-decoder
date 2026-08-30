<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * A vehicle model, as returned by the GetModelsForMake* endpoints.
 */
final readonly class Model
{
    public function __construct(
        public int $id,
        public string $name,
        public int $makeId,
        public string $makeName,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            id: Attr::int($row, ['Model_ID', 'ModelId', 'ModelID']),
            name: Attr::string($row, ['Model_Name', 'ModelName']),
            makeId: Attr::int($row, ['Make_ID', 'MakeId', 'MakeID']),
            makeName: Attr::string($row, ['Make_Name', 'MakeName']),
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

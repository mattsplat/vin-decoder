<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * An ORG (manufacturer regulatory) submission, as returned by GetParts.
 */
final readonly class Part
{
    public function __construct(
        public string $name,
        public string $type,
        public ?string $letterDate,
        public ?int $manufacturerId,
        public ?string $manufacturerName,
        public ?int $modelYearFrom,
        public ?int $modelYearTo,
        public ?string $url,
        public ?string $coverLetterUrl,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            name: Attr::string($row, 'Name'),
            type: Attr::string($row, 'Type'),
            letterDate: Attr::nullableString($row, 'LetterDate'),
            manufacturerId: Attr::nullableInt($row, 'ManufacturerId'),
            manufacturerName: Attr::nullableString($row, 'ManufacturerName'),
            modelYearFrom: Attr::nullableInt($row, 'ModelYearFrom'),
            modelYearTo: Attr::nullableInt($row, 'ModelYearTo'),
            url: Attr::nullableString($row, 'URL'),
            coverLetterUrl: Attr::nullableString($row, 'CoverLetterURL'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'letterDate' => $this->letterDate,
            'manufacturerId' => $this->manufacturerId,
            'manufacturerName' => $this->manufacturerName,
            'modelYearFrom' => $this->modelYearFrom,
            'modelYearTo' => $this->modelYearTo,
            'url' => $this->url,
            'coverLetterUrl' => $this->coverLetterUrl,
        ];
    }
}

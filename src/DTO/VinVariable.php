<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * One decoded variable/value row from DecodeVin / DecodeVinExtended.
 */
final readonly class VinVariable
{
    public function __construct(
        public int $variableId,
        public string $variable,
        public ?string $value,
        public ?string $valueId,
    ) {
    }

    /**
     * @param  array<string, mixed>  $row
     */
    public static function fromArray(array $row): self
    {
        return new self(
            variableId: Attr::int($row, 'VariableId'),
            variable: Attr::string($row, 'Variable'),
            value: Attr::nullableString($row, 'Value'),
            valueId: Attr::nullableString($row, 'ValueId'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'variableId' => $this->variableId,
            'variable' => $this->variable,
            'value' => $this->value,
            'valueId' => $this->valueId,
        ];
    }
}

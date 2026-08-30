<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\DTO;

/**
 * Internal helpers for reading loosely-typed vPIC result rows into DTOs.
 *
 * vPIC is inconsistent about key casing (`Make_ID` vs `MakeId`), uses empty
 * strings and `null` interchangeably for "no value", and returns numbers as
 * strings. These helpers smooth that over.
 *
 * @internal
 */
final class Attr
{
    /**
     * First non-null value found among $keys.
     *
     * @param  array<string, mixed>  $row
     * @param  string|list<string>  $keys
     */
    public static function raw(array $row, string|array $keys): mixed
    {
        foreach ((array) $keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null) {
                return $row[$key];
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  string|list<string>  $keys
     */
    public static function string(array $row, string|array $keys, string $default = ''): string
    {
        $value = self::raw($row, $keys);

        return $value === null ? $default : trim((string) $value);
    }

    /**
     * Like string(), but an empty/whitespace value becomes null.
     *
     * @param  array<string, mixed>  $row
     * @param  string|list<string>  $keys
     */
    public static function nullableString(array $row, string|array $keys): ?string
    {
        $value = self::string($row, $keys);

        return $value === '' ? null : $value;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  string|list<string>  $keys
     */
    public static function int(array $row, string|array $keys, int $default = 0): int
    {
        return self::nullableInt($row, $keys) ?? $default;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  string|list<string>  $keys
     */
    public static function nullableInt(array $row, string|array $keys): ?int
    {
        $value = self::raw($row, $keys);

        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  string|list<string>  $keys
     */
    public static function bool(array $row, string|array $keys): ?bool
    {
        $value = self::raw($row, $keys);

        if (is_bool($value)) {
            return $value;
        }

        if ($value === null || $value === '') {
            return null;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
    }
}

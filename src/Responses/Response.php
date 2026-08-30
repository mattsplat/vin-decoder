<?php

declare(strict_types=1);

namespace Mattsplat\VinDecode\Responses;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Mattsplat\VinDecode\Exceptions\VpicException;
use Traversable;

/**
 * A list response from the vPIC API: the `{Count, Message, SearchCriteria}`
 * envelope plus the mapped `Results` rows.
 *
 * Iterable and array-accessible over the mapped rows.
 *
 * @template T
 *
 * @implements IteratorAggregate<int, T>
 * @implements ArrayAccess<int, T>
 */
class Response implements ArrayAccess, Countable, IteratorAggregate
{
    /**
     * @param  list<T>  $results
     * @param  array<string, mixed>  $raw
     */
    public function __construct(
        public readonly array $results,
        public readonly int $reportedCount,
        public readonly string $message,
        public readonly ?string $searchCriteria,
        public readonly array $raw,
    ) {
    }

    /**
     * Build a Response from a decoded vPIC payload, mapping each result row
     * through $mapper.
     *
     * @param  array<string, mixed>  $payload
     * @param  callable(array<string, mixed>): T  $mapper
     * @return self<T>
     */
    public static function fromPayload(array $payload, callable $mapper): self
    {
        $rows = $payload['Results'] ?? [];

        if (! is_array($rows)) {
            $rows = [];
        }

        $results = [];
        foreach (array_values($rows) as $row) {
            $results[] = $mapper(is_array($row) ? $row : []);
        }

        return new self(
            $results,
            isset($payload['Count']) ? (int) $payload['Count'] : count($results),
            isset($payload['Message']) ? (string) $payload['Message'] : '',
            isset($payload['SearchCriteria']) ? (string) $payload['SearchCriteria'] : null,
            $payload,
        );
    }

    /**
     * All mapped rows.
     *
     * @return list<T>
     */
    public function all(): array
    {
        return $this->results;
    }

    /**
     * The first mapped row, or null when the response is empty.
     *
     * @return T|null
     */
    public function first()
    {
        return $this->results[0] ?? null;
    }

    /**
     * The number of rows actually returned in this response.
     *
     * Note: vPIC's own `Count` field (available via `$reportedCount`) can differ
     * for paginated endpoints.
     */
    public function count(): int
    {
        return count($this->results);
    }

    public function isEmpty(): bool
    {
        return $this->results === [];
    }

    /**
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->results);
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->results[$offset]);
    }

    /**
     * @return T
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->results[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new VpicException('vPIC responses are immutable.');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new VpicException('vPIC responses are immutable.');
    }
}

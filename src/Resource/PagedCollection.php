<?php

declare(strict_types=1);

namespace DrChrono\Resource;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Paged collection for API responses with pagination
 *
 * @template T
 * @implements IteratorAggregate<int, T>
 */
class PagedCollection implements IteratorAggregate, Countable
{
    private array $items;
    private ?string $nextUrl;
    private ?string $previousUrl;
    private int $totalCount;

    public function __construct(
        array $items = [],
        ?string $nextUrl = null,
        ?string $previousUrl = null,
        int $totalCount = 0
    ) {
        $this->items = $items;
        $this->nextUrl = $nextUrl;
        $this->previousUrl = $previousUrl;
        $this->totalCount = $totalCount ?: count($items);
    }

    public static function fromApiResponse(array $response): self
    {
        return new self(
            $response['results'] ?? [],
            $response['next'] ?? null,
            $response['previous'] ?? null,
            $response['count'] ?? count($response['results'] ?? [])
        );
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getNextUrl(): ?string
    {
        return $this->nextUrl;
    }

    public function getPreviousUrl(): ?string
    {
        return $this->previousUrl;
    }

    public function hasNext(): bool
    {
        return $this->nextUrl !== null;
    }

    public function hasPrevious(): bool
    {
        return $this->previousUrl !== null;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function first()
    {
        return $this->items[0] ?? null;
    }

    public function last()
    {
        return $this->items[count($this->items) - 1] ?? null;
    }
}

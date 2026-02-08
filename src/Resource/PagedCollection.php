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
    private mixed $items;
    private ?string $nextUrl;
    private ?string $previousUrl;
    private int $totalCount;

    public function __construct(
        mixed $items = [],
        ?string $nextUrl = null,
        ?string $previousUrl = null,
        int $totalCount = 0
    ) {
        $this->items = $items;
        $this->nextUrl = $nextUrl;
        $this->previousUrl = $previousUrl;
        $this->totalCount = $totalCount ?: count($items);
    }

    public static function fromApiResponse(array $response, bool $useLaravelCollections = false): self
    {
        $items = $response['results'] ?? [];

        if ($useLaravelCollections && class_exists(\Illuminate\Support\Collection::class)) {
            $items = new \Illuminate\Support\Collection($items);
        }

        return new self(
            $items,
            $response['next'] ?? null,
            $response['previous'] ?? null,
            $response['count'] ?? count($response['results'] ?? [])
        );
    }

    public function getItems(): mixed
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
        if ($this->items instanceof Traversable) {
            return $this->items;
        }

        return new ArrayIterator($this->items);
    }

    public function isEmpty(): bool
    {
        if (class_exists(\Illuminate\Support\Collection::class) && $this->items instanceof \Illuminate\Support\Collection) {
            return $this->items->isEmpty();
        }

        return empty($this->items);
    }

    public function first()
    {
        if (class_exists(\Illuminate\Support\Collection::class) && $this->items instanceof \Illuminate\Support\Collection) {
            return $this->items->first();
        }

        return $this->items[0] ?? null;
    }

    public function last()
    {
        if (class_exists(\Illuminate\Support\Collection::class) && $this->items instanceof \Illuminate\Support\Collection) {
            return $this->items->last();
        }

        return $this->items[count($this->items) - 1] ?? null;
    }

    public function filterItems(callable $callback): mixed
    {
        if (class_exists(\Illuminate\Support\Collection::class) && $this->items instanceof \Illuminate\Support\Collection) {
            return $this->items->filter($callback)->values();
        }

        return array_values(array_filter($this->items, $callback));
    }
}

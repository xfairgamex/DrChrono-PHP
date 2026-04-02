<?php

declare(strict_types=1);

namespace DrChrono\Resource;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Collection wrapper for bulk API responses
 *
 * Bulk responses use a different pagination shape than standard endpoints:
 * - Standard: {results, next, previous, count}
 * - Bulk:     {results, pagination: {count, pages, page_size, page}, status, uuid}
 */
class BulkOperationCollection implements IteratorAggregate, Countable
{
    private array $items;
    private array $pagination;
    private string $uuid;

    public function __construct(array $items, array $pagination, string $uuid)
    {
        $this->items = $items;
        $this->pagination = $pagination;
        $this->uuid = $uuid;
    }

    public static function fromBulkOperation(BulkOperation $operation): self
    {
        return new self(
            $operation->getResults(),
            $operation->getPagination(),
            $operation->getUuid() ?? ''
        );
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getTotalCount(): int
    {
        return (int) ($this->pagination['count'] ?? 0);
    }

    public function getTotalPages(): int
    {
        return (int) ($this->pagination['pages'] ?? 0);
    }

    public function getCurrentPage(): int
    {
        return (int) ($this->pagination['page'] ?? 0);
    }

    public function getPageSize(): int
    {
        return (int) ($this->pagination['page_size'] ?? 0);
    }

    public function hasMorePages(): bool
    {
        return $this->getCurrentPage() < $this->getTotalPages();
    }

    public function getPagination(): array
    {
        return $this->pagination;
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

    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    public function last(): mixed
    {
        return $this->items[count($this->items) - 1] ?? null;
    }
}

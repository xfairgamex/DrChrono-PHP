<?php

declare(strict_types=1);

namespace DrChrono\Tests;

use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;

class PagedCollectionTest extends TestCase
{
    public function testCreateFromApiResponse(): void
    {
        $response = [
            'results' => [
                ['id' => 1, 'name' => 'Item 1'],
                ['id' => 2, 'name' => 'Item 2'],
                ['id' => 3, 'name' => 'Item 3'],
            ],
            'next' => 'https://api.example.com/items?page=2',
            'previous' => null,
            'count' => 100,
        ];

        $collection = PagedCollection::fromApiResponse($response);

        $this->assertCount(3, $collection);
        $this->assertEquals(100, $collection->getTotalCount());
        $this->assertTrue($collection->hasNext());
        $this->assertFalse($collection->hasPrevious());
        $this->assertEquals('https://api.example.com/items?page=2', $collection->getNextUrl());
    }

    public function testIteration(): void
    {
        $items = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];

        $collection = new PagedCollection($items);

        $count = 0;
        foreach ($collection as $item) {
            $count++;
            $this->assertArrayHasKey('id', $item);
        }

        $this->assertEquals(3, $count);
    }

    public function testFirstAndLast(): void
    {
        $items = [
            ['id' => 1, 'name' => 'First'],
            ['id' => 2, 'name' => 'Middle'],
            ['id' => 3, 'name' => 'Last'],
        ];

        $collection = new PagedCollection($items);

        $this->assertEquals(['id' => 1, 'name' => 'First'], $collection->first());
        $this->assertEquals(['id' => 3, 'name' => 'Last'], $collection->last());
    }

    public function testEmptyCollection(): void
    {
        $collection = new PagedCollection();

        $this->assertTrue($collection->isEmpty());
        $this->assertCount(0, $collection);
        $this->assertNull($collection->first());
        $this->assertNull($collection->last());
        $this->assertFalse($collection->hasNext());
    }

    public function testGetItems(): void
    {
        $items = [
            ['id' => 1],
            ['id' => 2],
        ];

        $collection = new PagedCollection($items);

        $this->assertEquals($items, $collection->getItems());
    }
}

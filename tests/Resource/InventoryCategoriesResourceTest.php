<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\InventoryCategoriesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class InventoryCategoriesResourceTest extends TestCase
{
    private InventoryCategoriesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new InventoryCategoriesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Vaccines'],
                ['id' => 2, 'name' => 'Supplies'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/inventory_categories', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testGet(): void
    {
        $categoryId = 1;
        $expectedResponse = ['id' => 1, 'name' => 'Vaccines', 'description' => 'Vaccine inventory'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/inventory_categories/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($categoryId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateCategory(): void
    {
        $data = [
            'name' => 'Medical Supplies',
            'description' => 'General medical supplies',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/inventory_categories', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createCategory($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateCategory(): void
    {
        $categoryId = 1;
        $data = ['name' => 'Updated Name'];
        $expectedResponse = ['id' => 1, 'name' => 'Updated Name'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/inventory_categories/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateCategory($categoryId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteCategory(): void
    {
        $categoryId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/inventory_categories/1')
            ->willReturn([]);

        $this->resource->deleteCategory($categoryId);
    }

    public function testGetByName(): void
    {
        $name = 'Vaccines';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Vaccines'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/inventory_categories', ['name' => $name])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByName($name);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
    }

    public function testGetByNameNotFound(): void
    {
        $name = 'NonExistent';
        $expectedResponse = [
            'results' => [],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/inventory_categories', ['name' => $name])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByName($name);

        $this->assertNull($result);
    }

    public function testListOrdered(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Vaccines', 'sort_order' => 1],
                ['id' => 2, 'name' => 'Supplies', 'sort_order' => 2],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/inventory_categories', ['ordering' => 'sort_order'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listOrdered();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}

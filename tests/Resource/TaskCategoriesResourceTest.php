<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\TaskCategoriesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TaskCategoriesResourceTest extends TestCase
{
    private TaskCategoriesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new TaskCategoriesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Administrative'],
                ['id' => 2, 'name' => 'Clinical'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_categories', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testGet(): void
    {
        $categoryId = 1;
        $expectedResponse = ['id' => 1, 'name' => 'Administrative'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_categories/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($categoryId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateCategory(): void
    {
        $data = [
            'name' => 'Billing',
            'description' => 'Billing related tasks',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/task_categories', $data)
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
            ->with('/api/task_categories/1', $data)
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
            ->with('/api/task_categories/1')
            ->willReturn([]);

        $this->resource->deleteCategory($categoryId);
    }

    public function testGetByName(): void
    {
        $name = 'Administrative';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Administrative'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_categories', ['name' => $name])
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
            ->with('/api/task_categories', ['name' => $name])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByName($name);

        $this->assertNull($result);
    }

    public function testListActive(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Administrative', 'is_active' => true],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_categories', ['is_active' => 'true'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listActive();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListOrdered(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Administrative', 'sort_order' => 1],
                ['id' => 2, 'name' => 'Clinical', 'sort_order' => 2],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_categories', ['ordering' => 'sort_order'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listOrdered();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testArchive(): void
    {
        $categoryId = 1;
        $expectedResponse = ['id' => 1, 'is_active' => false];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/task_categories/1', ['is_active' => false])
            ->willReturn($expectedResponse);

        $result = $this->resource->archive($categoryId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testRestore(): void
    {
        $categoryId = 1;
        $expectedResponse = ['id' => 1, 'is_active' => true];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/task_categories/1', ['is_active' => true])
            ->willReturn($expectedResponse);

        $result = $this->resource->restore($categoryId);

        $this->assertEquals($expectedResponse, $result);
    }
}

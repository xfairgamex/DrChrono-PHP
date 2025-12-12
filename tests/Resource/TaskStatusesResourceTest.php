<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\TaskStatusesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TaskStatusesResourceTest extends TestCase
{
    private TaskStatusesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new TaskStatusesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Open'],
                ['id' => 2, 'name' => 'In Progress'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_statuses', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testGet(): void
    {
        $statusId = 1;
        $expectedResponse = ['id' => 1, 'name' => 'Open'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_statuses/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($statusId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateStatus(): void
    {
        $data = [
            'name' => 'Pending Review',
            'description' => 'Awaiting review',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/task_statuses', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createStatus($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateStatus(): void
    {
        $statusId = 1;
        $data = ['name' => 'Updated Name'];
        $expectedResponse = ['id' => 1, 'name' => 'Updated Name'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/task_statuses/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateStatus($statusId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteStatus(): void
    {
        $statusId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/task_statuses/1')
            ->willReturn([]);

        $this->resource->deleteStatus($statusId);
    }

    public function testGetByName(): void
    {
        $name = 'Open';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Open'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_statuses', ['name' => $name])
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
            ->with('/api/task_statuses', ['name' => $name])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByName($name);

        $this->assertNull($result);
    }

    public function testListActive(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Open', 'is_active' => true],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_statuses', ['is_active' => 'true'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listActive();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListOrdered(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Open', 'sort_order' => 1],
                ['id' => 2, 'name' => 'In Progress', 'sort_order' => 2],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_statuses', ['ordering' => 'sort_order'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listOrdered();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetDefault(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Open', 'is_default' => true],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_statuses', ['is_default' => 'true'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getDefault();

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
    }

    public function testListCompletionStatuses(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 3, 'name' => 'Completed', 'is_completed' => true],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_statuses', ['is_completed' => 'true'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listCompletionStatuses();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testArchive(): void
    {
        $statusId = 1;
        $expectedResponse = ['id' => 1, 'is_active' => false];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/task_statuses/1', ['is_active' => false])
            ->willReturn($expectedResponse);

        $result = $this->resource->archive($statusId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testRestore(): void
    {
        $statusId = 1;
        $expectedResponse = ['id' => 1, 'is_active' => true];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/task_statuses/1', ['is_active' => true])
            ->willReturn($expectedResponse);

        $result = $this->resource->restore($statusId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testSetAsDefault(): void
    {
        $statusId = 1;
        $expectedResponse = ['id' => 1, 'is_default' => true];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/task_statuses/1', ['is_default' => true])
            ->willReturn($expectedResponse);

        $result = $this->resource->setAsDefault($statusId);

        $this->assertEquals($expectedResponse, $result);
    }
}

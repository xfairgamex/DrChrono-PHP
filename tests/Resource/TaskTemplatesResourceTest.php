<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\TaskTemplatesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TaskTemplatesResourceTest extends TestCase
{
    private TaskTemplatesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new TaskTemplatesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'title' => 'Patient Follow-up'],
                ['id' => 2, 'title' => 'Insurance Verification'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_templates', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'title' => 'Patient Follow-up'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_templates', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByCategory(): void
    {
        $categoryId = 10;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'category' => 10, 'title' => 'Patient Follow-up'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_templates', ['category' => $categoryId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByCategory($categoryId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $templateId = 1;
        $expectedResponse = ['id' => 1, 'title' => 'Patient Follow-up'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_templates/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($templateId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateTemplate(): void
    {
        $data = [
            'title' => 'New Patient Setup',
            'description' => 'Initial patient setup tasks',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/task_templates', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createTemplate($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateTemplate(): void
    {
        $templateId = 1;
        $data = ['title' => 'Updated Title'];
        $expectedResponse = ['id' => 1, 'title' => 'Updated Title'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/task_templates/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateTemplate($templateId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteTemplate(): void
    {
        $templateId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/task_templates/1')
            ->willReturn([]);

        $this->resource->deleteTemplate($templateId);
    }

    public function testInstantiateTemplate(): void
    {
        $templateId = 1;
        $overrides = ['assigned_to' => 456];
        $expectedResponse = ['id' => 100, 'title' => 'Patient Follow-up', 'assigned_to' => 456];

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/task_templates/1/instantiate', $overrides)
            ->willReturn($expectedResponse);

        $result = $this->resource->instantiateTemplate($templateId, $overrides);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDuplicateTemplate(): void
    {
        $templateId = 1;
        $newTitle = 'Duplicated Template';
        $templateData = ['id' => 1, 'title' => 'Original', 'description' => 'Test'];
        $expectedResponse = ['id' => 2, 'title' => $newTitle, 'description' => 'Test'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_templates/1')
            ->willReturn($templateData);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/task_templates', ['title' => $newTitle, 'description' => 'Test'])
            ->willReturn($expectedResponse);

        $result = $this->resource->duplicateTemplate($templateId, $newTitle);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetByPriority(): void
    {
        $priority = 'high';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'priority' => 'high', 'title' => 'Urgent Task'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_templates', ['priority' => $priority])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByPriority($priority);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}

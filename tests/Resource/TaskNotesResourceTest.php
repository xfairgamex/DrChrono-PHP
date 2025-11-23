<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\TaskNotesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TaskNotesResourceTest extends TestCase
{
    private TaskNotesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new TaskNotesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'task' => 100, 'content' => 'Note 1'],
                ['id' => 2, 'task' => 101, 'content' => 'Note 2'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_notes', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testListByTask(): void
    {
        $taskId = 100;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'task' => 100, 'content' => 'Note 1'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_notes', ['task' => $taskId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByTask($taskId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByAuthor(): void
    {
        $userId = 456;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'author' => 456, 'content' => 'Note 1'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_notes', ['author' => $userId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByAuthor($userId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $noteId = 1;
        $expectedResponse = ['id' => 1, 'task' => 100, 'content' => 'Note content'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_notes/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($noteId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateNote(): void
    {
        $data = [
            'task' => 100,
            'content' => 'This is a new note',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/task_notes', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createNote($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateNote(): void
    {
        $noteId = 1;
        $data = ['content' => 'Updated content'];
        $expectedResponse = ['id' => 1, 'content' => 'Updated content'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/task_notes/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateNote($noteId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteNote(): void
    {
        $noteId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/task_notes/1')
            ->willReturn([]);

        $this->resource->deleteNote($noteId);
    }

    public function testAddQuickNote(): void
    {
        $taskId = 100;
        $content = 'Quick note';
        $expectedResponse = ['id' => 1, 'task' => 100, 'content' => 'Quick note'];

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/task_notes', ['task' => $taskId, 'content' => $content])
            ->willReturn($expectedResponse);

        $result = $this->resource->addQuickNote($taskId, $content);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testPin(): void
    {
        $noteId = 1;
        $expectedResponse = ['id' => 1, 'is_pinned' => true];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/task_notes/1', ['is_pinned' => true])
            ->willReturn($expectedResponse);

        $result = $this->resource->pin($noteId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUnpin(): void
    {
        $noteId = 1;
        $expectedResponse = ['id' => 1, 'is_pinned' => false];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/task_notes/1', ['is_pinned' => false])
            ->willReturn($expectedResponse);

        $result = $this->resource->unpin($noteId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetPinnedNotes(): void
    {
        $taskId = 100;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'task' => 100, 'is_pinned' => true],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_notes', ['task' => $taskId, 'is_pinned' => 'true'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getPinnedNotes($taskId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetTaskHistory(): void
    {
        $taskId = 100;
        $expectedResponse = [
            'results' => [
                ['id' => 2, 'task' => 100, 'created_at' => '2025-01-15'],
                ['id' => 1, 'task' => 100, 'created_at' => '2025-01-14'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_notes', ['task' => $taskId, 'ordering' => '-created_at'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getTaskHistory($taskId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetRecent(): void
    {
        $limit = 25;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'content' => 'Recent note'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/task_notes', ['ordering' => '-created_at', 'page_size' => $limit])
            ->willReturn($expectedResponse);

        $result = $this->resource->getRecent($limit);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\UserGroupsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class UserGroupsResourceTest extends TestCase
{
    private UserGroupsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new UserGroupsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Administrators', 'description' => 'System administrators'],
                ['id' => 2, 'name' => 'Providers', 'description' => 'Healthcare providers'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/user_groups', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testGet(): void
    {
        $expectedResponse = [
            'id' => 1,
            'name' => 'Administrators',
            'description' => 'System administrators',
            'permissions' => ['read', 'write', 'delete'],
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/user_groups/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get(1);

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals('Administrators', $result['name']);
    }

    public function testCreateGroup(): void
    {
        $data = [
            'name' => 'Nurses',
            'description' => 'Nursing staff',
            'permissions' => ['read', 'write'],
        ];

        $expectedResponse = array_merge(['id' => 3], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/user_groups', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createGroup($data);

        $this->assertEquals(3, $result['id']);
        $this->assertEquals('Nurses', $result['name']);
    }

    public function testUpdateGroup(): void
    {
        $data = ['description' => 'Updated description'];
        $expectedResponse = [
            'id' => 1,
            'name' => 'Administrators',
            'description' => 'Updated description',
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/user_groups/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateGroup(1, $data);

        $this->assertEquals('Updated description', $result['description']);
    }

    public function testDeleteGroup(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/user_groups/1')
            ->willReturn([]);

        $this->resource->deleteGroup(1);
    }

    public function testGetByName(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Administrators'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/user_groups', ['name' => 'Administrators'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByName('Administrators');

        $this->assertNotNull($result);
        $this->assertEquals('Administrators', $result['name']);
    }

    public function testGetByNameNotFound(): void
    {
        $expectedResponse = [
            'results' => [],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/user_groups', ['name' => 'NonExistent'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByName('NonExistent');

        $this->assertNull($result);
    }

    public function testSearch(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Administrators'],
                ['id' => 2, 'name' => 'Admin Team'],
                ['id' => 3, 'name' => 'Providers'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/user_groups', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->search('admin');

        $this->assertCount(2, $result);
        $this->assertEquals('Administrators', $result[0]['name']);
        $this->assertEquals('Admin Team', $result[1]['name']);
    }

    public function testGetGroupUsers(): void
    {
        $expectedResponse = [
            'id' => 1,
            'name' => 'Administrators',
            'users' => [
                ['id' => 10, 'name' => 'John Doe'],
                ['id' => 11, 'name' => 'Jane Smith'],
            ],
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/user_groups/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->getGroupUsers(1);

        $this->assertNotNull($result['users']);
        $this->assertCount(2, $result['users']);
    }

    public function testDuplicateGroup(): void
    {
        $sourceGroup = [
            'id' => 1,
            'name' => 'Original Group',
            'description' => 'Original description',
            'permissions' => ['read', 'write'],
            'created_at' => '2024-01-01',
            'updated_at' => '2024-01-02',
        ];

        $expectedNewGroup = [
            'id' => 2,
            'name' => 'Copied Group',
            'description' => 'Original description',
            'permissions' => ['read', 'write'],
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/user_groups/1')
            ->willReturn($sourceGroup);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/user_groups', [
                'name' => 'Copied Group',
                'description' => 'Original description',
                'permissions' => ['read', 'write'],
            ])
            ->willReturn($expectedNewGroup);

        $result = $this->resource->duplicateGroup(1, 'Copied Group');

        $this->assertEquals(2, $result['id']);
        $this->assertEquals('Copied Group', $result['name']);
        $this->assertEquals($sourceGroup['description'], $result['description']);
    }
}

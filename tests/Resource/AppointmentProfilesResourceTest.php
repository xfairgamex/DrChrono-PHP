<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\AppointmentProfilesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AppointmentProfilesResourceTest extends TestCase
{
    private AppointmentProfilesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new AppointmentProfilesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'New Patient', 'duration' => 60],
                ['id' => 2, 'name' => 'Follow-up', 'duration' => 30],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/appointment_profiles', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $items = $result->getItems();
        $this->assertCount(2, $items);
        $this->assertEquals('New Patient', $items[0]['name']);
    }

    public function testListWithFilters(): void
    {
        $filters = ['doctor' => 123, 'archived' => false];
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'New Patient', 'duration' => 60],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/appointment_profiles', $filters)
            ->willReturn($expectedResponse);

        $result = $this->resource->list($filters);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'New Patient', 'doctor' => 123],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/appointment_profiles', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
        $items = $result->getItems();
        $this->assertEquals(123, $items[0]['doctor']);
    }

    public function testListByOffice(): void
    {
        $officeId = 456;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'New Patient', 'office' => 456],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/appointment_profiles', ['office' => $officeId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByOffice($officeId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testCreateProfile(): void
    {
        $data = [
            'name' => 'Telehealth Visit',
            'duration' => 20,
            'doctor' => 123,
            'color' => '#4A90E2',
        ];
        $expectedResponse = ['id' => 1] + $data;

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/appointment_profiles', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createProfile($data);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Telehealth Visit', $result['name']);
    }

    public function testUpdateProfile(): void
    {
        $profileId = 1;
        $data = ['name' => 'Updated Name'];
        $expectedResponse = ['id' => 1, 'name' => 'Updated Name'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/appointment_profiles/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateProfile($profileId, $data);

        $this->assertEquals('Updated Name', $result['name']);
    }

    public function testArchive(): void
    {
        $profileId = 1;
        $expectedResponse = ['id' => 1, 'archived' => true];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/appointment_profiles/1', ['archived' => true])
            ->willReturn($expectedResponse);

        $result = $this->resource->archive($profileId);

        $this->assertTrue($result['archived']);
    }

    public function testGet(): void
    {
        $profileId = 1;
        $expectedResponse = ['id' => 1, 'name' => 'New Patient', 'duration' => 60];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/appointment_profiles/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($profileId);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('New Patient', $result['name']);
    }
}

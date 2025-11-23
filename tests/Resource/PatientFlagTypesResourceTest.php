<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\PatientFlagTypesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PatientFlagTypesResourceTest extends TestCase
{
    private PatientFlagTypesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new PatientFlagTypesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'VIP', 'color' => '#FF0000'],
                ['id' => 2, 'name' => 'High Risk', 'color' => '#FFA500'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_flag_types', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $items = $result->getItems();
        $this->assertCount(2, $items);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'name' => 'VIP'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_flag_types', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testCreateFlagType(): void
    {
        $data = [
            'name' => 'VIP',
            'color' => '#FF0000',
            'priority' => 1,
            'doctor' => 123,
        ];
        $expectedResponse = ['id' => 1] + $data;

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/patient_flag_types', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createFlagType($data);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('VIP', $result['name']);
    }

    public function testUpdateFlagType(): void
    {
        $flagTypeId = 1;
        $data = ['name' => 'Super VIP'];
        $expectedResponse = ['id' => 1, 'name' => 'Super VIP'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/patient_flag_types/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateFlagType($flagTypeId, $data);

        $this->assertEquals('Super VIP', $result['name']);
    }

    public function testDeleteFlagType(): void
    {
        $flagTypeId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/patient_flag_types/1')
            ->willReturn([]);

        $this->resource->deleteFlagType($flagTypeId);

        $this->assertTrue(true);
    }
}

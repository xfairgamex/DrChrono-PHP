<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\FeeSchedulesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class FeeSchedulesResourceTest extends TestCase
{
    private FeeSchedulesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new FeeSchedulesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Standard Fee Schedule', 'code' => '99213', 'price' => 150.00],
                ['id' => 2, 'name' => 'Medicare Fee Schedule', 'code' => '99214', 'price' => 200.00],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/fee_schedules', [])
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
                ['id' => 1, 'doctor' => 123, 'code' => '99213', 'price' => 150.00],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/fee_schedules', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $scheduleId = 1;
        $expectedResponse = ['id' => 1, 'name' => 'Standard Fee Schedule', 'price' => 150.00];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/fee_schedules/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($scheduleId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateSchedule(): void
    {
        $data = [
            'name' => 'New Fee Schedule',
            'code' => '99215',
            'price' => 250.00,
        ];
        $expectedResponse = array_merge(['id' => 3], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/fee_schedules', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createSchedule($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateSchedule(): void
    {
        $scheduleId = 1;
        $data = ['price' => 175.00];
        $expectedResponse = ['id' => 1, 'price' => 175.00];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/fee_schedules/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateSchedule($scheduleId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteSchedule(): void
    {
        $scheduleId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/fee_schedules/1')
            ->willReturn([]);

        $this->resource->deleteSchedule($scheduleId);
    }

    public function testGetByCode(): void
    {
        $code = '99213';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'code' => '99213', 'price' => 150.00],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/fee_schedules', ['code' => $code])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByCode($code);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}

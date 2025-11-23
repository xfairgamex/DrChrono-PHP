<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\ProceduresResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ProceduresResourceTest extends TestCase
{
    private ProceduresResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new ProceduresResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'code' => '99213', 'description' => 'Office visit'],
                ['id' => 2, 'patient' => 457, 'code' => '99214', 'description' => 'Extended visit'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/procedures', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testListByPatient(): void
    {
        $patientId = 456;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'code' => '99213'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/procedures', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'code' => '99213'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/procedures', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByAppointment(): void
    {
        $appointmentId = 789;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'appointment' => 789, 'code' => '99213'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/procedures', ['appointment' => $appointmentId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByAppointment($appointmentId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $procedureId = 1;
        $expectedResponse = ['id' => 1, 'code' => '99213', 'description' => 'Office visit'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/procedures/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($procedureId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateProcedure(): void
    {
        $data = [
            'patient' => 456,
            'code' => '99213',
            'description' => 'Office visit',
            'date' => '2025-11-23',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/procedures', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createProcedure($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateProcedure(): void
    {
        $procedureId = 1;
        $data = ['notes' => 'Updated notes'];
        $expectedResponse = ['id' => 1, 'notes' => 'Updated notes'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/procedures/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateProcedure($procedureId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteProcedure(): void
    {
        $procedureId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/procedures/1')
            ->willReturn([]);

        $this->resource->deleteProcedure($procedureId);
    }

    public function testGetByCode(): void
    {
        $code = '99213';
        $mockData = [
            ['id' => 1, 'code' => '99213', 'patient' => 456],
            ['id' => 2, 'code' => '99214', 'patient' => 457],
            ['id' => 3, 'code' => '99213', 'patient' => 458],
        ];
        $expectedResponse = [
            'results' => $mockData,
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/procedures', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByCode($code);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }

    public function testListByDateRange(): void
    {
        $startDate = '2025-11-01';
        $endDate = '2025-11-30';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'code' => '99213', 'date' => '2025-11-15'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/procedures', ['date_range' => '2025-11-01/2025-11-30'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDateRange($startDate, $endDate);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}

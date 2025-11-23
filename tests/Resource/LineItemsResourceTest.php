<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\LineItemsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class LineItemsResourceTest extends TestCase
{
    private LineItemsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new LineItemsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'code' => '99213', 'appointment' => 789],
                ['id' => 2, 'code' => '99214', 'appointment' => 790],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/line_items', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
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
            ->with('/api/line_items', ['appointment' => $appointmentId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByAppointment($appointmentId);

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
            ->with('/api/line_items', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
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
            ->with('/api/line_items', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $lineItemId = 1;
        $expectedResponse = ['id' => 1, 'code' => '99213', 'price' => 150.00];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/line_items/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($lineItemId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateLineItem(): void
    {
        $data = [
            'appointment' => 789,
            'code' => '99213',
            'procedure_type' => 'CPT',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/line_items', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createLineItem($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateLineItem(): void
    {
        $lineItemId = 1;
        $data = ['price' => 175.00];
        $expectedResponse = ['id' => 1, 'price' => 175.00];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/line_items/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateLineItem($lineItemId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteLineItem(): void
    {
        $lineItemId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/line_items/1')
            ->willReturn([]);

        $this->resource->deleteLineItem($lineItemId);
    }

    public function testListByCode(): void
    {
        $code = '99213';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'code' => '99213'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/line_items', ['code' => $code])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByCode($code);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testAddProcedure(): void
    {
        $appointmentId = 789;
        $code = '99213';
        $expectedData = [
            'appointment' => $appointmentId,
            'code' => $code,
            'procedure_type' => 'CPT',
        ];
        $expectedResponse = array_merge(['id' => 1], $expectedData);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/line_items', $expectedData)
            ->willReturn($expectedResponse);

        $result = $this->resource->addProcedure($appointmentId, $code);

        $this->assertEquals($expectedResponse, $result);
    }
}

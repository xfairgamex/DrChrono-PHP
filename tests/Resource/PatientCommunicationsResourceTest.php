<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\PatientCommunicationsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PatientCommunicationsResourceTest extends TestCase
{
    private PatientCommunicationsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new PatientCommunicationsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'communication_type' => 'Follow-up', 'method' => 'phone'],
                ['id' => 2, 'patient' => 457, 'communication_type' => 'Education', 'method' => 'email'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_communications', [])
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
                ['id' => 1, 'patient' => 456, 'communication_type' => 'Follow-up'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_communications', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'communication_type' => 'Follow-up'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_communications', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $communicationId = 1;
        $expectedResponse = ['id' => 1, 'communication_type' => 'Follow-up', 'method' => 'phone'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_communications/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($communicationId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateCommunication(): void
    {
        $data = [
            'patient' => 456,
            'communication_type' => 'Follow-up',
            'date' => '2025-11-23',
            'method' => 'phone',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/patient_communications', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createCommunication($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateCommunication(): void
    {
        $communicationId = 1;
        $data = ['outcome' => 'Patient scheduled for follow-up'];
        $expectedResponse = ['id' => 1, 'outcome' => 'Patient scheduled for follow-up'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/patient_communications/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateCommunication($communicationId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteCommunication(): void
    {
        $communicationId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/patient_communications/1')
            ->willReturn([]);

        $this->resource->deleteCommunication($communicationId);
    }

    public function testGetRequiringFollowUp(): void
    {
        $mockData = [
            ['id' => 1, 'patient' => 456, 'follow_up_required' => true],
            ['id' => 2, 'patient' => 457, 'follow_up_required' => false],
            ['id' => 3, 'patient' => 458, 'follow_up_required' => true],
        ];
        $expectedResponse = [
            'results' => $mockData,
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_communications', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->getRequiringFollowUp();

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }

    public function testGetByType(): void
    {
        $type = 'Follow-up';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'type' => 'Follow-up'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_communications', ['type' => $type])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByType($type);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetByMethod(): void
    {
        $method = 'email';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'method' => 'email'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_communications', ['method' => $method])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByMethod($method);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDateRange(): void
    {
        $startDate = '2025-11-01';
        $endDate = '2025-11-30';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'date' => '2025-11-15'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_communications', ['date_range' => '2025-11-01/2025-11-30'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDateRange($startDate, $endDate);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}

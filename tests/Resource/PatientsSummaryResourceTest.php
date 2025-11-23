<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\PatientsSummaryResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PatientsSummaryResourceTest extends TestCase
{
    private PatientsSummaryResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new PatientsSummaryResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient_id' => 789, 'visit_count' => 5],
                ['id' => 2, 'patient_id' => 790, 'visit_count' => 3],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patients_summary', [])
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
                ['id' => 1, 'doctor' => 123, 'patient_id' => 789],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patients_summary', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListDetailed(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient_id' => 789, 'detailed_info' => []],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patients_summary', ['verbose' => 'true'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listDetailed();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetSummary(): void
    {
        $patientId = 789;
        $expectedResponse = ['id' => 1, 'patient_id' => 789, 'visit_count' => 5];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patients_summary/789')
            ->willReturn($expectedResponse);

        $result = $this->resource->getSummary($patientId);

        $this->assertEquals(789, $result['patient_id']);
        $this->assertEquals(5, $result['visit_count']);
    }

    public function testGetDetailedSummary(): void
    {
        $patientId = 789;
        $expectedResponse = ['id' => 1, 'patient_id' => 789, 'detailed_info' => []];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patients_summary/789', ['verbose' => 'true'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getDetailedSummary($patientId);

        $this->assertEquals(789, $result['patient_id']);
    }
}

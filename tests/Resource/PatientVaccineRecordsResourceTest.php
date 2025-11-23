<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\PatientVaccineRecordsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PatientVaccineRecordsResourceTest extends TestCase
{
    private PatientVaccineRecordsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new PatientVaccineRecordsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'vaccine' => 789],
                ['id' => 2, 'patient' => 457, 'vaccine' => 790],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_vaccine_records', [])
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
                ['id' => 1, 'patient' => 456, 'vaccine' => 789],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_vaccine_records', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'vaccine' => 789],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_vaccine_records', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByVaccine(): void
    {
        $vaccineId = 789;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'vaccine' => 789],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_vaccine_records', ['vaccine' => $vaccineId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByVaccine($vaccineId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $recordId = 1;
        $expectedResponse = ['id' => 1, 'patient' => 456, 'vaccine' => 789];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_vaccine_records/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($recordId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateRecord(): void
    {
        $data = [
            'patient' => 456,
            'vaccine' => 789,
            'administered_at' => '2025-01-15',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/patient_vaccine_records', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createRecord($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateRecord(): void
    {
        $recordId = 1;
        $data = ['notes' => 'Updated notes'];
        $expectedResponse = ['id' => 1, 'notes' => 'Updated notes'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/patient_vaccine_records/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateRecord($recordId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteRecord(): void
    {
        $recordId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/patient_vaccine_records/1')
            ->willReturn([]);

        $this->resource->deleteRecord($recordId);
    }

    public function testListByDateRange(): void
    {
        $startDate = '2025-01-01';
        $endDate = '2025-01-31';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'administered_at' => '2025-01-15'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_vaccine_records', ['administered_at__range' => '2025-01-01,2025-01-31'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDateRange($startDate, $endDate);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetImmunizationHistory(): void
    {
        $patientId = 456;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'administered_at' => '2025-01-15'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_vaccine_records', ['patient' => $patientId, 'ordering' => '-administered_at'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getImmunizationHistory($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetByLotNumber(): void
    {
        $lotNumber = 'LOT123';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'lot_number' => 'LOT123'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_vaccine_records', ['lot_number' => $lotNumber])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByLotNumber($lotNumber);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}

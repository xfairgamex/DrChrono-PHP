<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\PatientPhysicalExamsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PatientPhysicalExamsResourceTest extends TestCase
{
    private PatientPhysicalExamsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new PatientPhysicalExamsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'date' => '2025-11-20'],
                ['id' => 2, 'patient' => 457, 'date' => '2025-11-21'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_physical_exams', [])
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
                ['id' => 1, 'patient' => 456, 'date' => '2025-11-20'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_physical_exams', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'date' => '2025-11-20'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_physical_exams', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByAppointment(): void
    {
        $appointmentId = 789;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'appointment' => 789, 'date' => '2025-11-20'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_physical_exams', ['appointment' => $appointmentId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByAppointment($appointmentId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $examId = 1;
        $expectedResponse = ['id' => 1, 'patient' => 456, 'date' => '2025-11-20'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_physical_exams/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($examId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateExam(): void
    {
        $data = [
            'patient' => 456,
            'date' => '2025-11-23',
            'general_appearance' => 'Well-nourished, well-developed',
            'cardiovascular' => 'Regular rate and rhythm',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/patient_physical_exams', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createExam($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateExam(): void
    {
        $examId = 1;
        $data = ['notes' => 'Updated findings'];
        $expectedResponse = ['id' => 1, 'notes' => 'Updated findings'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/patient_physical_exams/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateExam($examId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteExam(): void
    {
        $examId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/patient_physical_exams/1')
            ->willReturn([]);

        $this->resource->deleteExam($examId);
    }

    public function testGetMostRecent(): void
    {
        $patientId = 456;
        $mockData = [
            ['id' => 1, 'patient' => 456, 'date' => '2025-11-10'],
            ['id' => 2, 'patient' => 456, 'date' => '2025-11-20'],
            ['id' => 3, 'patient' => 456, 'date' => '2025-11-15'],
        ];
        $expectedResponse = [
            'results' => $mockData,
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_physical_exams', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->getMostRecent($patientId);

        $this->assertNotNull($result);
        $this->assertEquals(2, $result['id']);
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
            ->with('/api/patient_physical_exams', ['date_range' => '2025-11-01/2025-11-30'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDateRange($startDate, $endDate);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}

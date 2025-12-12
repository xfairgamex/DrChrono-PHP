<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\PatientRiskAssessmentsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PatientRiskAssessmentsResourceTest extends TestCase
{
    private PatientRiskAssessmentsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new PatientRiskAssessmentsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'assessment_type' => 'Fall Risk', 'risk_level' => 'high'],
                ['id' => 2, 'patient' => 457, 'assessment_type' => 'Suicide Risk', 'risk_level' => 'low'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_risk_assessments', [])
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
                ['id' => 1, 'patient' => 456, 'assessment_type' => 'Fall Risk'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_risk_assessments', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'assessment_type' => 'Fall Risk'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_risk_assessments', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $assessmentId = 1;
        $expectedResponse = ['id' => 1, 'assessment_type' => 'Fall Risk', 'risk_level' => 'high'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_risk_assessments/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($assessmentId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateAssessment(): void
    {
        $data = [
            'patient' => 456,
            'assessment_type' => 'Fall Risk',
            'date' => '2025-11-23',
            'risk_level' => 'high',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/patient_risk_assessments', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createAssessment($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateAssessment(): void
    {
        $assessmentId = 1;
        $data = ['risk_level' => 'medium'];
        $expectedResponse = ['id' => 1, 'risk_level' => 'medium'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/patient_risk_assessments/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateAssessment($assessmentId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteAssessment(): void
    {
        $assessmentId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/patient_risk_assessments/1')
            ->willReturn([]);

        $this->resource->deleteAssessment($assessmentId);
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
            ->with('/api/patient_risk_assessments', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->getMostRecent($patientId);

        $this->assertNotNull($result);
        $this->assertEquals(2, $result['id']);
    }

    public function testGetMostRecentWithType(): void
    {
        $patientId = 456;
        $assessmentType = 'Fall Risk';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'assessment_type' => 'Fall Risk', 'date' => '2025-11-20'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_risk_assessments', [
                'patient' => $patientId,
                'assessment_type' => $assessmentType,
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->getMostRecent($patientId, $assessmentType);

        $this->assertNotNull($result);
        $this->assertEquals(1, $result['id']);
    }

    public function testGetHighRisk(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'risk_level' => 'high'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_risk_assessments', ['risk_level' => 'high'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getHighRisk();

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
            ->with('/api/patient_risk_assessments', ['date_range' => '2025-11-01/2025-11-30'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDateRange($startDate, $endDate);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}

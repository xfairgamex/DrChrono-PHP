<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\PatientInterventionsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PatientInterventionsResourceTest extends TestCase
{
    private PatientInterventionsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new PatientInterventionsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'intervention_type' => 'Medication', 'status' => 'active'],
                ['id' => 2, 'patient' => 457, 'intervention_type' => 'Exercise', 'status' => 'active'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_interventions', [])
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
                ['id' => 1, 'patient' => 456, 'intervention_type' => 'Medication'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_interventions', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'intervention_type' => 'Medication'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_interventions', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByCarePlan(): void
    {
        $carePlanId = 100;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'care_plan' => 100, 'intervention_type' => 'Medication'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_interventions', ['care_plan' => $carePlanId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByCarePlan($carePlanId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $interventionId = 1;
        $expectedResponse = ['id' => 1, 'intervention_type' => 'Medication', 'status' => 'active'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_interventions/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($interventionId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateIntervention(): void
    {
        $data = [
            'patient' => 456,
            'intervention_type' => 'Medication',
            'description' => 'Insulin therapy',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/patient_interventions', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createIntervention($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateIntervention(): void
    {
        $interventionId = 1;
        $data = ['status' => 'completed'];
        $expectedResponse = ['id' => 1, 'status' => 'completed'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/patient_interventions/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateIntervention($interventionId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteIntervention(): void
    {
        $interventionId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/patient_interventions/1')
            ->willReturn([]);

        $this->resource->deleteIntervention($interventionId);
    }

    public function testGetActiveForPatient(): void
    {
        $patientId = 456;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'status' => 'active'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_interventions', ['patient' => $patientId, 'status' => 'active'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getActiveForPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testMarkCompleted(): void
    {
        $interventionId = 1;
        $outcome = 'Goals achieved successfully';
        $today = date('Y-m-d');
        $expectedResponse = [
            'id' => 1,
            'status' => 'completed',
            'outcome' => $outcome,
            'end_date' => $today,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/patient_interventions/1', [
                'status' => 'completed',
                'outcome' => $outcome,
                'end_date' => $today,
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->markCompleted($interventionId, $outcome);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDiscontinue(): void
    {
        $interventionId = 1;
        $reason = 'Patient intolerance';
        $today = date('Y-m-d');
        $expectedResponse = [
            'id' => 1,
            'status' => 'discontinued',
            'discontinuation_reason' => $reason,
            'end_date' => $today,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/patient_interventions/1', [
                'status' => 'discontinued',
                'discontinuation_reason' => $reason,
                'end_date' => $today,
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->discontinue($interventionId, $reason);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetByType(): void
    {
        $interventionType = 'Medication';
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'intervention_type' => 'Medication'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_interventions', ['intervention_type' => $interventionType])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByType($interventionType);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}

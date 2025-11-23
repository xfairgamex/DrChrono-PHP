<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\CarePlansResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CarePlansResourceTest extends TestCase
{
    private CarePlansResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new CarePlansResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'title' => 'Diabetes Management'],
                ['id' => 2, 'patient' => 457, 'title' => 'Hypertension Care'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/care_plans', [])
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
                ['id' => 1, 'patient' => 456, 'title' => 'Diabetes Management'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/care_plans', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'title' => 'Diabetes Management'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/care_plans', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $carePlanId = 1;
        $expectedResponse = ['id' => 1, 'title' => 'Diabetes Management', 'status' => 'active'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/care_plans/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($carePlanId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateCarePlan(): void
    {
        $data = [
            'patient' => 456,
            'title' => 'Weight Loss Program',
            'description' => 'Comprehensive weight management',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/care_plans', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createCarePlan($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateCarePlan(): void
    {
        $carePlanId = 1;
        $data = ['status' => 'completed'];
        $expectedResponse = ['id' => 1, 'status' => 'completed'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/care_plans/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateCarePlan($carePlanId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteCarePlan(): void
    {
        $carePlanId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/care_plans/1')
            ->willReturn([]);

        $this->resource->deleteCarePlan($carePlanId);
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
            ->with('/api/care_plans', ['patient' => $patientId, 'status' => 'active'])
            ->willReturn($expectedResponse);

        $result = $this->resource->getActiveForPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testMarkCompleted(): void
    {
        $carePlanId = 1;
        $today = date('Y-m-d');
        $expectedResponse = [
            'id' => 1,
            'status' => 'completed',
            'end_date' => $today,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/care_plans/1', [
                'status' => 'completed',
                'end_date' => $today,
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->markCompleted($carePlanId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testMarkCompletedWithCustomDate(): void
    {
        $carePlanId = 1;
        $completionDate = '2025-11-20';
        $expectedResponse = [
            'id' => 1,
            'status' => 'completed',
            'end_date' => $completionDate,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/care_plans/1', [
                'status' => 'completed',
                'end_date' => $completionDate,
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->markCompleted($carePlanId, $completionDate);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCancel(): void
    {
        $carePlanId = 1;
        $reason = 'Patient transferred to another facility';
        $expectedResponse = [
            'id' => 1,
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/care_plans/1', [
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->cancel($carePlanId, $reason);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testAddGoal(): void
    {
        $carePlanId = 1;
        $goal = [
            'description' => 'Reduce HbA1c to below 7%',
            'target_date' => '2026-02-01',
        ];
        $existingGoals = [
            ['description' => 'Exercise 3x per week'],
        ];
        $carePlan = [
            'id' => 1,
            'title' => 'Diabetes Management',
            'goals' => $existingGoals,
        ];
        $updatedGoals = array_merge($existingGoals, [$goal]);
        $expectedResponse = [
            'id' => 1,
            'title' => 'Diabetes Management',
            'goals' => $updatedGoals,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/care_plans/1')
            ->willReturn($carePlan);

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/care_plans/1', ['goals' => $updatedGoals])
            ->willReturn($expectedResponse);

        $result = $this->resource->addGoal($carePlanId, $goal);

        $this->assertEquals($expectedResponse, $result);
        $this->assertCount(2, $result['goals']);
    }
}

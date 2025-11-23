<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\CustomInsurancePlanNamesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CustomInsurancePlanNamesResourceTest extends TestCase
{
    private CustomInsurancePlanNamesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new CustomInsurancePlanNamesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'insurance_plan' => 123, 'custom_name' => 'My Custom Plan Name'],
                ['id' => 2, 'insurance_plan' => 124, 'custom_name' => 'Another Custom Name'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/custom_insurance_plan_names', [])
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
                ['id' => 1, 'doctor' => 123, 'custom_name' => 'Custom Plan'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/custom_insurance_plan_names', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $planId = 1;
        $expectedResponse = ['id' => 1, 'insurance_plan' => 123, 'custom_name' => 'My Custom Plan'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/custom_insurance_plan_names/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($planId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreatePlanName(): void
    {
        $data = [
            'insurance_plan' => 123,
            'custom_name' => 'My Custom Plan Name',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/custom_insurance_plan_names', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createPlanName($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdatePlanName(): void
    {
        $planId = 1;
        $data = ['custom_name' => 'Updated Name'];
        $expectedResponse = ['id' => 1, 'custom_name' => 'Updated Name'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/custom_insurance_plan_names/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updatePlanName($planId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeletePlanName(): void
    {
        $planId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/custom_insurance_plan_names/1')
            ->willReturn([]);

        $this->resource->deletePlanName($planId);
    }

    public function testSetCustomName(): void
    {
        $insurancePlanId = 123;
        $customName = 'My Custom Plan';
        $expectedData = [
            'insurance_plan' => $insurancePlanId,
            'custom_name' => $customName,
        ];
        $expectedResponse = array_merge(['id' => 1], $expectedData);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/custom_insurance_plan_names', $expectedData)
            ->willReturn($expectedResponse);

        $result = $this->resource->setCustomName($insurancePlanId, $customName);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testSetCustomNameWithDoctor(): void
    {
        $insurancePlanId = 123;
        $customName = 'My Custom Plan';
        $doctorId = 456;
        $expectedData = [
            'insurance_plan' => $insurancePlanId,
            'custom_name' => $customName,
            'doctor' => $doctorId,
        ];
        $expectedResponse = array_merge(['id' => 1], $expectedData);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/custom_insurance_plan_names', $expectedData)
            ->willReturn($expectedResponse);

        $result = $this->resource->setCustomName($insurancePlanId, $customName, $doctorId);

        $this->assertEquals($expectedResponse, $result);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\CustomDemographicsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CustomDemographicsResourceTest extends TestCase
{
    private CustomDemographicsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new CustomDemographicsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Emergency Contact', 'field_type' => 'text'],
                ['id' => 2, 'name' => 'Preferred Language', 'field_type' => 'select'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/custom_demographics', [])
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
                ['id' => 1, 'doctor' => 123, 'name' => 'Custom Field'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/custom_demographics', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testCreateField(): void
    {
        $data = [
            'name' => 'Emergency Contact',
            'field_type' => 'text',
            'doctor' => 123,
        ];
        $expectedResponse = ['id' => 1] + $data;

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/custom_demographics', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createField($data);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Emergency Contact', $result['name']);
    }

    public function testUpdateField(): void
    {
        $fieldId = 1;
        $data = ['name' => 'Updated Field Name'];
        $expectedResponse = ['id' => 1, 'name' => 'Updated Field Name'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/custom_demographics/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateField($fieldId, $data);

        $this->assertEquals('Updated Field Name', $result['name']);
    }

    public function testDeleteField(): void
    {
        $fieldId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/custom_demographics/1')
            ->willReturn([]);

        $this->resource->deleteField($fieldId);

        $this->assertTrue(true);
    }
}

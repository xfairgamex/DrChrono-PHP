<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\ClinicalNoteFieldTypesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ClinicalNoteFieldTypesResourceTest extends TestCase
{
    private ClinicalNoteFieldTypesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new ClinicalNoteFieldTypesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Blood Pressure', 'data_type' => 'number'],
                ['id' => 2, 'name' => 'Chief Complaint', 'data_type' => 'text'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_types', [])
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
                ['id' => 1, 'doctor' => 123, 'name' => 'Blood Pressure'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_types', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $fieldTypeId = 1;
        $expectedResponse = ['id' => 1, 'name' => 'Blood Pressure', 'data_type' => 'number'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_types/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($fieldTypeId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateFieldType(): void
    {
        $data = [
            'name' => 'Temperature',
            'data_type' => 'number',
            'is_required' => true,
        ];
        $expectedResponse = array_merge(['id' => 3], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/clinical_note_field_types', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createFieldType($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateFieldType(): void
    {
        $fieldTypeId = 1;
        $data = ['is_required' => false];
        $expectedResponse = ['id' => 1, 'is_required' => false];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/clinical_note_field_types/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateFieldType($fieldTypeId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteFieldType(): void
    {
        $fieldTypeId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/clinical_note_field_types/1')
            ->willReturn([]);

        $this->resource->deleteFieldType($fieldTypeId);
    }

    public function testGetByDataType(): void
    {
        $mockData = [
            ['id' => 1, 'name' => 'Blood Pressure', 'data_type' => 'number'],
            ['id' => 2, 'name' => 'Chief Complaint', 'data_type' => 'text'],
            ['id' => 3, 'name' => 'Temperature', 'data_type' => 'number'],
        ];
        $expectedResponse = [
            'results' => $mockData,
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_types', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByDataType('number');

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }

    public function testGetByDataTypeWithFilters(): void
    {
        $doctorId = 123;
        $mockData = [
            ['id' => 1, 'name' => 'Blood Pressure', 'data_type' => 'text', 'doctor' => 123],
            ['id' => 2, 'name' => 'Notes', 'data_type' => 'text', 'doctor' => 123],
        ];
        $expectedResponse = [
            'results' => $mockData,
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_types', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->getByDataType('text', ['doctor' => $doctorId]);

        $this->assertCount(2, $result);
    }
}

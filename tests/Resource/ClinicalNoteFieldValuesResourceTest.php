<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\ClinicalNoteFieldValuesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ClinicalNoteFieldValuesResourceTest extends TestCase
{
    private ClinicalNoteFieldValuesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new ClinicalNoteFieldValuesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'clinical_note' => 100, 'field_type' => 1, 'value' => '120/80'],
                ['id' => 2, 'clinical_note' => 100, 'field_type' => 2, 'value' => 'Headache'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_values', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testListByClinicalNote(): void
    {
        $clinicalNoteId = 100;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'clinical_note' => 100, 'field_type' => 1, 'value' => '120/80'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_values', ['clinical_note' => $clinicalNoteId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByClinicalNote($clinicalNoteId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByFieldType(): void
    {
        $fieldTypeId = 1;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'clinical_note' => 100, 'field_type' => 1, 'value' => '120/80'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_values', ['field_type' => $fieldTypeId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByFieldType($fieldTypeId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $fieldValueId = 1;
        $expectedResponse = ['id' => 1, 'clinical_note' => 100, 'field_type' => 1, 'value' => '120/80'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_values/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($fieldValueId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateFieldValue(): void
    {
        $data = [
            'clinical_note' => 100,
            'field_type' => 1,
            'value' => '120/80',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/clinical_note_field_values', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createFieldValue($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateFieldValue(): void
    {
        $fieldValueId = 1;
        $data = ['value' => '130/85'];
        $expectedResponse = ['id' => 1, 'value' => '130/85'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/clinical_note_field_values/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateFieldValue($fieldValueId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteFieldValue(): void
    {
        $fieldValueId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/clinical_note_field_values/1')
            ->willReturn([]);

        $this->resource->deleteFieldValue($fieldValueId);
    }

    public function testUpsertValueCreatesNew(): void
    {
        $clinicalNoteId = 100;
        $fieldTypeId = 1;
        $value = '120/80';
        $expectedResponse = [
            'results' => [],
            'next' => null,
            'previous' => null,
        ];
        $createData = [
            'clinical_note' => $clinicalNoteId,
            'field_type' => $fieldTypeId,
            'value' => $value,
        ];
        $createdResponse = array_merge(['id' => 1], $createData);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_values', ['clinical_note' => $clinicalNoteId])
            ->willReturn($expectedResponse);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/clinical_note_field_values', $createData)
            ->willReturn($createdResponse);

        $result = $this->resource->upsertValue($clinicalNoteId, $fieldTypeId, $value);

        $this->assertEquals($createdResponse, $result);
    }

    public function testUpsertValueUpdatesExisting(): void
    {
        $clinicalNoteId = 100;
        $fieldTypeId = 1;
        $value = '130/85';
        $existingData = [
            'results' => [
                ['id' => 5, 'clinical_note' => 100, 'field_type' => 1, 'value' => '120/80'],
            ],
            'next' => null,
            'previous' => null,
        ];
        $updateData = ['value' => $value];
        $updatedResponse = ['id' => 5, 'value' => $value];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_field_values', ['clinical_note' => $clinicalNoteId])
            ->willReturn($existingData);

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/clinical_note_field_values/5', $updateData)
            ->willReturn($updatedResponse);

        $result = $this->resource->upsertValue($clinicalNoteId, $fieldTypeId, $value);

        $this->assertEquals($updatedResponse, $result);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\ClinicalNoteTemplatesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ClinicalNoteTemplatesResourceTest extends TestCase
{
    private ClinicalNoteTemplatesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new ClinicalNoteTemplatesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'SOAP Note', 'doctor' => 123],
                ['id' => 2, 'name' => 'Progress Note', 'doctor' => 123],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_templates', [])
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
                ['id' => 1, 'doctor' => 123, 'name' => 'SOAP Note'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_templates', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $templateId = 1;
        $expectedResponse = ['id' => 1, 'name' => 'SOAP Note', 'doctor' => 123];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_templates/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($templateId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateTemplate(): void
    {
        $data = [
            'name' => 'New Template',
            'doctor' => 123,
            'content' => 'Template content',
        ];
        $expectedResponse = array_merge(['id' => 3], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/clinical_note_templates', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createTemplate($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateTemplate(): void
    {
        $templateId = 1;
        $data = ['is_default' => true];
        $expectedResponse = ['id' => 1, 'is_default' => true];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/clinical_note_templates/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateTemplate($templateId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteTemplate(): void
    {
        $templateId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/clinical_note_templates/1')
            ->willReturn([]);

        $this->resource->deleteTemplate($templateId);
    }

    public function testGetDefaultTemplates(): void
    {
        $doctorId = 123;
        $mockData = [
            ['id' => 1, 'doctor' => 123, 'is_default' => true],
            ['id' => 2, 'doctor' => 123, 'is_default' => false],
            ['id' => 3, 'doctor' => 123, 'is_default' => true],
        ];
        $expectedResponse = [
            'results' => $mockData,
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_templates', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->getDefaultTemplates($doctorId);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }

    public function testCloneTemplate(): void
    {
        $templateId = 1;
        $newName = 'Cloned Template';
        $sourceData = [
            'id' => 1,
            'name' => 'Original Template',
            'doctor' => 123,
            'content' => 'Template content',
            'sections' => ['History', 'Exam'],
        ];
        $createData = [
            'name' => $newName,
            'doctor' => 123,
            'content' => 'Template content',
            'is_default' => false,
            'sections' => ['History', 'Exam'],
        ];
        $expectedResponse = array_merge(['id' => 2], $createData);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_templates/1')
            ->willReturn($sourceData);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/clinical_note_templates', $createData)
            ->willReturn($expectedResponse);

        $result = $this->resource->cloneTemplate($templateId, $newName);

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals($newName, $result['name']);
    }

    public function testCloneTemplateWithDifferentDoctor(): void
    {
        $templateId = 1;
        $newName = 'Cloned Template';
        $newDoctorId = 456;
        $sourceData = [
            'id' => 1,
            'name' => 'Original Template',
            'doctor' => 123,
            'content' => 'Template content',
        ];
        $createData = [
            'name' => $newName,
            'doctor' => $newDoctorId,
            'content' => 'Template content',
            'is_default' => false,
        ];
        $expectedResponse = array_merge(['id' => 2], $createData);

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/clinical_note_templates/1')
            ->willReturn($sourceData);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/clinical_note_templates', $createData)
            ->willReturn($expectedResponse);

        $result = $this->resource->cloneTemplate($templateId, $newName, $newDoctorId);

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals($newDoctorId, $result['doctor']);
    }
}

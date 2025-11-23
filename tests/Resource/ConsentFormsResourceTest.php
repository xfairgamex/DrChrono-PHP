<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\ConsentFormsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ConsentFormsResourceTest extends TestCase
{
    private ConsentFormsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new ConsentFormsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'title' => 'HIPAA Consent', 'patient' => 456],
                ['id' => 2, 'title' => 'Treatment Consent', 'patient' => 457],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/consent_forms', [])
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
                ['id' => 1, 'patient' => 456, 'title' => 'HIPAA Consent'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/consent_forms', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'title' => 'HIPAA Consent'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/consent_forms', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $consentId = 1;
        $expectedResponse = ['id' => 1, 'title' => 'HIPAA Consent', 'is_signed' => true];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/consent_forms/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($consentId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateForm(): void
    {
        $data = [
            'patient' => 456,
            'title' => 'New Consent Form',
        ];
        $expectedResponse = array_merge(['id' => 3], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/consent_forms', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createForm($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateForm(): void
    {
        $consentId = 1;
        $data = ['is_signed' => true];
        $expectedResponse = ['id' => 1, 'is_signed' => true];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/consent_forms/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateForm($consentId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteForm(): void
    {
        $consentId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/consent_forms/1')
            ->willReturn([]);

        $this->resource->deleteForm($consentId);
    }

    public function testMarkAsSigned(): void
    {
        $consentId = 1;
        $today = date('Y-m-d');
        $expectedData = [
            'is_signed' => true,
            'signed_date' => $today,
        ];
        $expectedResponse = ['id' => 1, 'is_signed' => true, 'signed_date' => $today];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/consent_forms/1', $expectedData)
            ->willReturn($expectedResponse);

        $result = $this->resource->markAsSigned($consentId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetUnsignedForms(): void
    {
        $patientId = 456;
        $mockData = [
            ['id' => 1, 'patient' => 456, 'is_signed' => false],
            ['id' => 2, 'patient' => 456, 'is_signed' => true],
            ['id' => 3, 'patient' => 456, 'is_signed' => false],
        ];
        $expectedResponse = [
            'results' => $mockData,
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/consent_forms', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->getUnsignedForms($patientId);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }
}

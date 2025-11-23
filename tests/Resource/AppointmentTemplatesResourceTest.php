<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\AppointmentTemplatesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AppointmentTemplatesResourceTest extends TestCase
{
    private AppointmentTemplatesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new AppointmentTemplatesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'day_of_week' => 1, 'start_time' => '09:00'],
                ['id' => 2, 'day_of_week' => 2, 'start_time' => '10:00'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/appointment_templates', [])
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
                ['id' => 1, 'doctor' => 123, 'day_of_week' => 1],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/appointment_templates', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByOffice(): void
    {
        $officeId = 456;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'office' => 456],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/appointment_templates', ['office' => $officeId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByOffice($officeId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testCreateTemplate(): void
    {
        $data = [
            'doctor' => 123,
            'profile' => 1,
            'day_of_week' => 1,
            'start_time' => '09:00',
            'duration' => 60,
        ];
        $expectedResponse = ['id' => 1] + $data;

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/appointment_templates', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createTemplate($data);

        $this->assertEquals(1, $result['id']);
    }

    public function testUpdateTemplate(): void
    {
        $templateId = 1;
        $data = ['start_time' => '10:00'];
        $expectedResponse = ['id' => 1, 'start_time' => '10:00'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/appointment_templates/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateTemplate($templateId, $data);

        $this->assertEquals('10:00', $result['start_time']);
    }

    public function testDeleteTemplate(): void
    {
        $templateId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/appointment_templates/1')
            ->willReturn([]);

        $this->resource->deleteTemplate($templateId);

        // If no exception is thrown, the test passes
        $this->assertTrue(true);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\PatientMessagesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PatientMessagesResourceTest extends TestCase
{
    private PatientMessagesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new PatientMessagesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'message' => 'Test message', 'read' => false],
                ['id' => 2, 'message' => 'Another message', 'read' => true],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_messages', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $items = $result->getItems();
        $this->assertCount(2, $items);
    }

    public function testListByPatient(): void
    {
        $patientId = 789;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 789, 'message' => 'Test message'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_messages', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListUnread(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'message' => 'Unread message', 'read' => false],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_messages', ['read' => false])
            ->willReturn($expectedResponse);

        $result = $this->resource->listUnread();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $items = $result->getItems();
        $this->assertCount(1, $items);
        $this->assertFalse($items[0]['read']);
    }

    public function testSendMessage(): void
    {
        $data = [
            'patient' => 789,
            'message' => 'Your appointment is confirmed.',
        ];
        $expectedResponse = ['id' => 1] + $data + ['read' => false];

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/patient_messages', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->sendMessage($data);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Your appointment is confirmed.', $result['message']);
    }

    public function testMarkAsRead(): void
    {
        $messageId = 1;
        $expectedResponse = ['id' => 1, 'read' => true];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/patient_messages/1', ['read' => true])
            ->willReturn($expectedResponse);

        $result = $this->resource->markAsRead($messageId);

        $this->assertTrue($result['read']);
    }

    public function testMarkAsUnread(): void
    {
        $messageId = 1;
        $expectedResponse = ['id' => 1, 'read' => false];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/patient_messages/1', ['read' => false])
            ->willReturn($expectedResponse);

        $result = $this->resource->markAsUnread($messageId);

        $this->assertFalse($result['read']);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\PrescriptionMessagesResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PrescriptionMessagesResourceTest extends TestCase
{
    private PrescriptionMessagesResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new PrescriptionMessagesResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                [
                    'id' => 1,
                    'patient' => 100,
                    'doctor' => 200,
                    'message_type' => 'refill_request',
                    'status' => 'pending',
                ],
                [
                    'id' => 2,
                    'patient' => 101,
                    'doctor' => 201,
                    'message_type' => 'new_rx',
                    'status' => 'sent',
                ],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/prescription_messages', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testGet(): void
    {
        $expectedResponse = [
            'id' => 1,
            'patient' => 100,
            'doctor' => 200,
            'prescription' => 300,
            'message_type' => 'refill_request',
            'content' => 'Patient requests refill',
            'status' => 'pending',
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/prescription_messages/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get(1);

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals('refill_request', $result['message_type']);
    }

    public function testCreateMessage(): void
    {
        $data = [
            'patient' => 100,
            'doctor' => 200,
            'prescription' => 300,
            'message_type' => 'refill_request',
            'content' => 'Refill request',
        ];

        $expectedResponse = array_merge(['id' => 1, 'status' => 'pending'], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/prescription_messages', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createMessage($data);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('pending', $result['status']);
    }

    public function testUpdateMessage(): void
    {
        $data = ['status' => 'responded'];
        $expectedResponse = [
            'id' => 1,
            'patient' => 100,
            'status' => 'responded',
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/prescription_messages/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateMessage(1, $data);

        $this->assertEquals('responded', $result['status']);
    }

    public function testDeleteMessage(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/prescription_messages/1')
            ->willReturn([]);

        $this->resource->deleteMessage(1);
    }

    public function testListByPatient(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 100],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/prescription_messages', ['patient' => 100])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient(100);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 200],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/prescription_messages', ['doctor' => 200])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor(200);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByPrescription(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'prescription' => 300],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/prescription_messages', ['prescription' => 300])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPrescription(300);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByStatus(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'status' => 'pending'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/prescription_messages', ['status' => 'pending'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByStatus('pending');

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByType(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'message_type' => 'refill_request'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/prescription_messages', ['message_type' => 'refill_request'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByType('refill_request');

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetPendingRefillRequests(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'message_type' => 'refill_request', 'status' => 'pending'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/prescription_messages', [
                'message_type' => 'refill_request',
                'status' => 'pending',
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->getPendingRefillRequests();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetPendingRefillRequestsWithDoctor(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 200, 'message_type' => 'refill_request', 'status' => 'pending'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/prescription_messages', [
                'message_type' => 'refill_request',
                'status' => 'pending',
                'doctor' => 200,
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->getPendingRefillRequests(200);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetUnreadByDoctor(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 200, 'is_read' => false],
                ['id' => 2, 'doctor' => 200, 'is_read' => true],
                ['id' => 3, 'doctor' => 200, 'is_read' => false],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/prescription_messages', ['doctor' => 200])
            ->willReturn($expectedResponse);

        $result = $this->resource->getUnreadByDoctor(200);

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }

    public function testMarkAsRead(): void
    {
        $expectedResponse = [
            'id' => 1,
            'is_read' => true,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/prescription_messages/1', ['is_read' => true])
            ->willReturn($expectedResponse);

        $result = $this->resource->markAsRead(1);

        $this->assertTrue($result['is_read']);
    }

    public function testMarkAsUnread(): void
    {
        $expectedResponse = [
            'id' => 1,
            'is_read' => false,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/prescription_messages/1', ['is_read' => false])
            ->willReturn($expectedResponse);

        $result = $this->resource->markAsUnread(1);

        $this->assertFalse($result['is_read']);
    }

    public function testRespond(): void
    {
        $expectedResponse = [
            'id' => 1,
            'response' => 'Approved',
            'status' => 'responded',
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/prescription_messages/1', [
                'response' => 'Approved',
                'status' => 'responded',
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->respond(1, 'Approved');

        $this->assertEquals('Approved', $result['response']);
        $this->assertEquals('responded', $result['status']);
    }

    public function testGetMessageHistory(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'prescription' => 300, 'created_at' => '2024-01-03'],
                ['id' => 2, 'prescription' => 300, 'created_at' => '2024-01-01'],
                ['id' => 3, 'prescription' => 300, 'created_at' => '2024-01-02'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/prescription_messages', ['prescription' => 300])
            ->willReturn($expectedResponse);

        $result = $this->resource->getMessageHistory(300);

        $this->assertCount(3, $result);
        // Should be sorted by date
        $this->assertEquals('2024-01-01', $result[0]['created_at']);
        $this->assertEquals('2024-01-02', $result[1]['created_at']);
        $this->assertEquals('2024-01-03', $result[2]['created_at']);
    }
}

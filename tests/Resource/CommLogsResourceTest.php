<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\CommLogsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CommLogsResourceTest extends TestCase
{
    private CommLogsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new CommLogsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                [
                    'id' => 1,
                    'patient' => 100,
                    'communication_type' => 'phone',
                    'direction' => 'inbound',
                    'duration_minutes' => 15,
                ],
                [
                    'id' => 2,
                    'patient' => 101,
                    'communication_type' => 'email',
                    'direction' => 'outbound',
                ],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', [])
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
            'communication_type' => 'phone',
            'direction' => 'inbound',
            'duration_minutes' => 15,
            'notes' => 'Patient called regarding appointment',
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get(1);

        $this->assertEquals($expectedResponse, $result);
        $this->assertEquals('phone', $result['communication_type']);
    }

    public function testCreateLog(): void
    {
        $data = [
            'patient' => 100,
            'doctor' => 200,
            'communication_type' => 'phone',
            'direction' => 'outbound',
            'duration_minutes' => 10,
            'notes' => 'Follow-up call',
        ];

        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/comm_logs', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createLog($data);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('phone', $result['communication_type']);
    }

    public function testUpdateLog(): void
    {
        $data = ['notes' => 'Updated notes'];
        $expectedResponse = [
            'id' => 1,
            'patient' => 100,
            'notes' => 'Updated notes',
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/comm_logs/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateLog(1, $data);

        $this->assertEquals('Updated notes', $result['notes']);
    }

    public function testDeleteLog(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/comm_logs/1')
            ->willReturn([]);

        $this->resource->deleteLog(1);
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
            ->with('/api/comm_logs', ['patient' => 100])
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
            ->with('/api/comm_logs', ['doctor' => 200])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor(200);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByUser(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'user' => 300],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', ['user' => 300])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByUser(300);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByType(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'communication_type' => 'phone'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', ['communication_type' => 'phone'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByType('phone');

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDateRange(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'date' => '2024-01-01'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', ['date_range' => '2024-01-01/2024-01-31'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDateRange('2024-01-01', '2024-01-31');

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListPhoneCalls(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'communication_type' => 'phone'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', ['communication_type' => 'phone'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listPhoneCalls();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListEmails(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'communication_type' => 'email'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', ['communication_type' => 'email'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listEmails();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListTextMessages(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'communication_type' => 'text'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', ['communication_type' => 'text'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listTextMessages();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListInbound(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'direction' => 'inbound'],
                ['id' => 2, 'direction' => 'outbound'],
                ['id' => 3, 'direction' => 'inbound'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->listInbound();

        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }

    public function testListOutbound(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'direction' => 'inbound'],
                ['id' => 2, 'direction' => 'outbound'],
                ['id' => 3, 'direction' => 'outbound'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->listOutbound();

        $this->assertCount(2, $result);
        $this->assertEquals(2, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
    }

    public function testLogPhoneCall(): void
    {
        $expectedResponse = [
            'id' => 1,
            'patient' => 100,
            'communication_type' => 'phone',
            'direction' => 'outbound',
            'duration_minutes' => 15,
            'notes' => 'Follow-up call',
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/comm_logs', [
                'patient' => 100,
                'communication_type' => 'phone',
                'direction' => 'outbound',
                'duration_minutes' => 15,
                'notes' => 'Follow-up call',
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->logPhoneCall(100, 'outbound', 15, 'Follow-up call');

        $this->assertEquals('phone', $result['communication_type']);
        $this->assertEquals(15, $result['duration_minutes']);
    }

    public function testLogEmail(): void
    {
        $expectedResponse = [
            'id' => 1,
            'patient' => 100,
            'communication_type' => 'email',
            'direction' => 'outbound',
            'subject' => 'Test Results',
            'notes' => 'Sent test results to patient',
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/comm_logs', [
                'patient' => 100,
                'communication_type' => 'email',
                'direction' => 'outbound',
                'subject' => 'Test Results',
                'notes' => 'Sent test results to patient',
            ])
            ->willReturn($expectedResponse);

        $result = $this->resource->logEmail(100, 'outbound', 'Test Results', 'Sent test results to patient');

        $this->assertEquals('email', $result['communication_type']);
        $this->assertEquals('Test Results', $result['subject']);
    }

    public function testGetPatientHistory(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 100, 'date' => '2024-01-03', 'created_at' => '2024-01-03'],
                ['id' => 2, 'patient' => 100, 'date' => '2024-01-01', 'created_at' => '2024-01-01'],
                ['id' => 3, 'patient' => 100, 'date' => '2024-01-02', 'created_at' => '2024-01-02'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', ['patient' => 100])
            ->willReturn($expectedResponse);

        $result = $this->resource->getPatientHistory(100);

        $this->assertCount(3, $result);
        // Should be sorted by date descending (newest first)
        $this->assertEquals('2024-01-03', $result[0]['date']);
        $this->assertEquals('2024-01-02', $result[1]['date']);
        $this->assertEquals('2024-01-01', $result[2]['date']);
    }

    public function testGetRecent(): void
    {
        $expectedResponse = [
            'results' => array_map(fn($i) => ['id' => $i], range(1, 100)),
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->getRecent(25);

        $this->assertCount(25, $result);
    }

    public function testGetRecentDefaultLimit(): void
    {
        $expectedResponse = [
            'results' => array_map(fn($i) => ['id' => $i], range(1, 100)),
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/comm_logs', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->getRecent();

        $this->assertCount(50, $result);
    }
}

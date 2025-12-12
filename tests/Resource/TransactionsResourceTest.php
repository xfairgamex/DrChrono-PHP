<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\TransactionsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TransactionsResourceTest extends TestCase
{
    private TransactionsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new TransactionsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'amount' => 50.00, 'transaction_type' => 'Payment'],
                ['id' => 2, 'amount' => 25.00, 'transaction_type' => 'Adjustment'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/transactions', [])
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
                ['id' => 1, 'patient' => 456, 'amount' => 50.00],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/transactions', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByAppointment(): void
    {
        $appointmentId = 789;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'appointment' => 789, 'amount' => 50.00],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/transactions', ['appointment' => $appointmentId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByAppointment($appointmentId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'amount' => 50.00],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/transactions', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $transactionId = 1;
        $expectedResponse = ['id' => 1, 'amount' => 50.00, 'transaction_type' => 'Payment'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/transactions/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($transactionId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreateTransaction(): void
    {
        $data = [
            'appointment' => 789,
            'amount' => 50.00,
            'transaction_type' => 'Payment',
        ];
        $expectedResponse = array_merge(['id' => 1], $data);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/transactions', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createTransaction($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdateTransaction(): void
    {
        $transactionId = 1;
        $data = ['amount' => 75.00];
        $expectedResponse = ['id' => 1, 'amount' => 75.00];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/transactions/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updateTransaction($transactionId, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDeleteTransaction(): void
    {
        $transactionId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/transactions/1')
            ->willReturn([]);

        $this->resource->deleteTransaction($transactionId);
    }

    public function testRecordPayment(): void
    {
        $appointmentId = 789;
        $amount = 50.00;
        $expectedData = [
            'appointment' => $appointmentId,
            'amount' => $amount,
            'transaction_type' => 'Payment',
        ];
        $expectedResponse = array_merge(['id' => 1], $expectedData);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/transactions', $expectedData)
            ->willReturn($expectedResponse);

        $result = $this->resource->recordPayment($appointmentId, $amount);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testRecordAdjustment(): void
    {
        $appointmentId = 789;
        $amount = 25.00;
        $expectedData = [
            'appointment' => $appointmentId,
            'amount' => $amount,
            'transaction_type' => 'Adjustment',
        ];
        $expectedResponse = array_merge(['id' => 2], $expectedData);

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/transactions', $expectedData)
            ->willReturn($expectedResponse);

        $result = $this->resource->recordAdjustment($appointmentId, $amount);

        $this->assertEquals($expectedResponse, $result);
    }
}

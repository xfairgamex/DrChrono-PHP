<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\PatientPaymentLogResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PatientPaymentLogResourceTest extends TestCase
{
    private PatientPaymentLogResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new PatientPaymentLogResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'action' => 'created'],
                ['id' => 2, 'patient' => 456, 'action' => 'updated'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_payment_log', [])
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
                ['id' => 1, 'patient' => 456, 'action' => 'created'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_payment_log', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByPayment(): void
    {
        $paymentId = 789;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'payment' => 789, 'action' => 'created'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_payment_log', ['payment' => $paymentId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPayment($paymentId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByDoctor(): void
    {
        $doctorId = 123;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'doctor' => 123, 'action' => 'created'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_payment_log', ['doctor' => $doctorId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByDoctor($doctorId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGet(): void
    {
        $logId = 1;
        $expectedResponse = ['id' => 1, 'patient' => 456, 'action' => 'created'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_payment_log/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get($logId);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testGetPaymentHistory(): void
    {
        $patientId = 456;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'patient' => 456, 'action' => 'created'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_payment_log', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->getPaymentHistory($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testGetRecentActivity(): void
    {
        $days = 30;
        $expectedSince = date('Y-m-d', strtotime("-{$days} days"));
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'action' => 'created'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_payment_log', ['since' => $expectedSince])
            ->willReturn($expectedResponse);

        $result = $this->resource->getRecentActivity($days);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\PatientPaymentsResource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class PatientPaymentsResourceTest extends TestCase
{
    private PatientPaymentsResource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new PatientPaymentsResource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'amount' => 50.00, 'payment_method' => 'Credit Card'],
                ['id' => 2, 'amount' => 25.00, 'payment_method' => 'Cash'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_payments', [])
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
                ['id' => 1, 'patient' => 789, 'amount' => 50.00],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_payments', ['patient' => $patientId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByPatient($patientId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testListByAppointment(): void
    {
        $appointmentId = 456;
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'appointment' => 456, 'amount' => 50.00],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/patient_payments', ['appointment' => $appointmentId])
            ->willReturn($expectedResponse);

        $result = $this->resource->listByAppointment($appointmentId);

        $this->assertInstanceOf(PagedCollection::class, $result);
    }

    public function testCreatePayment(): void
    {
        $data = [
            'patient' => 789,
            'appointment' => 456,
            'amount' => 50.00,
            'payment_method' => 'Credit Card',
        ];
        $expectedResponse = ['id' => 1] + $data;

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/patient_payments', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->createPayment($data);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals(50.00, $result['amount']);
    }

    public function testUpdatePayment(): void
    {
        $paymentId = 1;
        $data = ['amount' => 75.00];
        $expectedResponse = ['id' => 1, 'amount' => 75.00];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/patient_payments/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->updatePayment($paymentId, $data);

        $this->assertEquals(75.00, $result['amount']);
    }

    public function testDeletePayment(): void
    {
        $paymentId = 1;

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/patient_payments/1')
            ->willReturn([]);

        $this->resource->deletePayment($paymentId);

        $this->assertTrue(true);
    }
}

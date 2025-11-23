<?php

declare(strict_types=1);

namespace DrChrono\Tests\Model;

use DrChrono\Model\PatientPayment;
use PHPUnit\Framework\TestCase;

class PatientPaymentTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'patient' => 789,
            'appointment' => 456,
            'amount' => 50.00,
            'payment_method' => 'Credit Card',
            'payment_date' => '2025-11-23',
            'notes' => 'Copay payment',
        ];

        $model = PatientPayment::fromArray($data);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals(789, $model->getPatientId());
        $this->assertEquals(456, $model->getAppointmentId());
        $this->assertEquals(50.00, $model->getAmount());
        $this->assertEquals('Credit Card', $model->getPaymentMethod());
        $this->assertEquals('2025-11-23', $model->getPaymentDate());
        $this->assertEquals('Copay payment', $model->getNotes());
    }

    public function testToArray(): void
    {
        $model = new PatientPayment();
        $model->setPatient(789)
              ->setAppointment(456)
              ->setAmount(75.00)
              ->setPaymentMethod('Cash')
              ->setPaymentDate('2025-11-24')
              ->setNotes('Full payment');

        $array = $model->toArray();

        $this->assertEquals(789, $array['patient']);
        $this->assertEquals(456, $array['appointment']);
        $this->assertEquals(75.00, $array['amount']);
        $this->assertEquals('Cash', $array['payment_method']);
        $this->assertEquals('2025-11-24', $array['payment_date']);
        $this->assertEquals('Full payment', $array['notes']);
    }

    public function testChaining(): void
    {
        $model = (new PatientPayment())
            ->setPatient(111)
            ->setAppointment(222)
            ->setAmount(100.00)
            ->setPaymentMethod('Check')
            ->setNotes('Test payment');

        $this->assertEquals(111, $model->getPatientId());
        $this->assertEquals(222, $model->getAppointmentId());
        $this->assertEquals(100.00, $model->getAmount());
        $this->assertEquals('Check', $model->getPaymentMethod());
        $this->assertEquals('Test payment', $model->getNotes());
    }

    public function testNullDefaults(): void
    {
        $model = new PatientPayment();

        $this->assertNull($model->getId());
        $this->assertNull($model->getPatientId());
        $this->assertNull($model->getAppointmentId());
        $this->assertNull($model->getAmount());
        $this->assertNull($model->getPaymentMethod());
        $this->assertNull($model->getPaymentDate());
        $this->assertNull($model->getNotes());
    }

    public function testSnakeCaseConversion(): void
    {
        $data = [
            'payment_method' => 'Debit Card',
            'payment_date' => '2025-11-25',
        ];

        $model = PatientPayment::fromArray($data);

        $this->assertEquals('Debit Card', $model->getPaymentMethod());
        $this->assertEquals('2025-11-25', $model->getPaymentDate());
    }
}

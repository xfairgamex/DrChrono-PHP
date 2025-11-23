<?php

declare(strict_types=1);

namespace DrChrono\Tests\Model;

use DrChrono\Model\Transaction;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'appointment' => 789,
            'amount' => 50.00,
            'transaction_type' => 'Payment',
            'posted_date' => '2025-11-23',
            'check_number' => 'CHK123',
            'ins_name' => 'Blue Cross',
            'note' => 'Copay payment',
        ];

        $model = Transaction::fromArray($data);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals(789, $model->getAppointmentId());
        $this->assertEquals(50.00, $model->getAmount());
        $this->assertEquals('Payment', $model->getTransactionType());
        $this->assertEquals('2025-11-23', $model->getPostedDate());
        $this->assertEquals('CHK123', $model->getCheckNumber());
        $this->assertEquals('Blue Cross', $model->getInsuranceName());
        $this->assertEquals('Copay payment', $model->getNote());
    }

    public function testToArray(): void
    {
        $model = new Transaction();
        $model->setAppointment(789)
              ->setAmount(50.00)
              ->setTransactionType('Payment')
              ->setNote('Test payment');

        $array = $model->toArray();

        $this->assertEquals(789, $array['appointment']);
        $this->assertEquals(50.00, $array['amount']);
        $this->assertEquals('Payment', $array['transaction_type']);
        $this->assertEquals('Test payment', $array['note']);
    }

    public function testChaining(): void
    {
        $model = (new Transaction())
            ->setAppointment(789)
            ->setAmount(75.00)
            ->setTransactionType('Adjustment');

        $this->assertEquals(789, $model->getAppointmentId());
        $this->assertEquals(75.00, $model->getAmount());
        $this->assertEquals('Adjustment', $model->getTransactionType());
    }

    public function testNullDefaults(): void
    {
        $model = new Transaction();

        $this->assertNull($model->getId());
        $this->assertNull($model->getAppointmentId());
        $this->assertNull($model->getAmount());
        $this->assertNull($model->getTransactionType());
    }

    public function testIsPayment(): void
    {
        $model = new Transaction();
        $model->setTransactionType('Payment');

        $this->assertTrue($model->isPayment());
        $this->assertFalse($model->isAdjustment());
    }

    public function testIsAdjustment(): void
    {
        $model = new Transaction();
        $model->setTransactionType('Adjustment');

        $this->assertTrue($model->isAdjustment());
        $this->assertFalse($model->isPayment());
    }
}

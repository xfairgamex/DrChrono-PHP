<?php

declare(strict_types=1);

namespace DrChrono\Tests\Model;

use DrChrono\Model\PatientMessage;
use PHPUnit\Framework\TestCase;

class PatientMessageTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'patient' => 789,
            'doctor' => 123,
            'message' => 'Your appointment is confirmed.',
            'read' => false,
            'sent_at' => '2025-11-23T10:00:00Z',
        ];

        $model = PatientMessage::fromArray($data);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals(789, $model->getPatientId());
        $this->assertEquals(123, $model->getDoctorId());
        $this->assertEquals('Your appointment is confirmed.', $model->getMessage());
        $this->assertFalse($model->isRead());
        $this->assertEquals('2025-11-23T10:00:00Z', $model->getSentAt());
    }

    public function testToArray(): void
    {
        $model = new PatientMessage();
        $model->setPatient(789)
              ->setDoctor(123)
              ->setMessage('Please arrive 15 minutes early.')
              ->setRead(true)
              ->setSentAt('2025-11-24T11:00:00Z');

        $array = $model->toArray();

        $this->assertEquals(789, $array['patient']);
        $this->assertEquals(123, $array['doctor']);
        $this->assertEquals('Please arrive 15 minutes early.', $array['message']);
        $this->assertTrue($array['read']);
        $this->assertEquals('2025-11-24T11:00:00Z', $array['sent_at']);
    }

    public function testChaining(): void
    {
        $model = (new PatientMessage())
            ->setPatient(111)
            ->setDoctor(222)
            ->setMessage('Test message')
            ->setRead(false);

        $this->assertEquals(111, $model->getPatientId());
        $this->assertEquals(222, $model->getDoctorId());
        $this->assertEquals('Test message', $model->getMessage());
        $this->assertFalse($model->isRead());
    }

    public function testNullDefaults(): void
    {
        $model = new PatientMessage();

        $this->assertNull($model->getId());
        $this->assertNull($model->getPatientId());
        $this->assertNull($model->getDoctorId());
        $this->assertNull($model->getMessage());
        $this->assertFalse($model->isRead());
        $this->assertNull($model->getSentAt());
    }

    public function testIsRead(): void
    {
        $model = new PatientMessage();
        $this->assertFalse($model->isRead());

        $model->setRead(true);
        $this->assertTrue($model->isRead());
    }

    public function testSnakeCaseConversion(): void
    {
        $data = [
            'sent_at' => '2025-11-25T12:00:00Z',
        ];

        $model = PatientMessage::fromArray($data);

        $this->assertEquals('2025-11-25T12:00:00Z', $model->getSentAt());
    }
}

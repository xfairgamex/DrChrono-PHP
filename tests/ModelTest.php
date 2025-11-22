<?php

declare(strict_types=1);

namespace DrChrono\Tests;

use DrChrono\Model\Patient;
use DrChrono\Model\Appointment;
use DrChrono\Model\User;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    public function testPatientFromArray(): void
    {
        $data = [
            'id' => 123,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'middle_name' => 'M',
            'email' => 'john@example.com',
            'date_of_birth' => '1980-01-15',
            'gender' => 'Male',
            'chart_id' => 'CHART123',
        ];

        $patient = Patient::fromArray($data);

        $this->assertEquals(123, $patient->getId());
        $this->assertEquals('John', $patient->getFirstName());
        $this->assertEquals('Doe', $patient->getLastName());
        $this->assertEquals('john@example.com', $patient->getEmail());
        $this->assertEquals('1980-01-15', $patient->getDateOfBirth());
        $this->assertEquals('Male', $patient->getGender());
        $this->assertEquals('CHART123', $patient->getChartId());
    }

    public function testPatientToArray(): void
    {
        $patient = new Patient();
        $patient->setFirstName('Jane')
            ->setLastName('Smith')
            ->setEmail('jane@example.com')
            ->setDateOfBirth('1985-03-20')
            ->setGender('Female')
            ->setDoctor(456);

        $array = $patient->toArray();

        $this->assertIsArray($array);
        $this->assertEquals('Jane', $array['first_name']);
        $this->assertEquals('Smith', $array['last_name']);
        $this->assertEquals('jane@example.com', $array['email']);
        $this->assertEquals('1985-03-20', $array['date_of_birth']);
        $this->assertEquals('Female', $array['gender']);
        $this->assertEquals(456, $array['doctor']);
    }

    public function testPatientFullName(): void
    {
        $patient = Patient::fromArray([
            'first_name' => 'John',
            'middle_name' => 'M',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('John M Doe', $patient->getFullName());
    }

    public function testAppointmentModel(): void
    {
        $appointment = new Appointment();
        $appointment->setDoctor(123)
            ->setPatient(456)
            ->setOffice(1)
            ->setDuration(30)
            ->setScheduledTime('2025-01-15T10:00:00')
            ->setStatus('Scheduled')
            ->setReason('Annual checkup');

        $this->assertEquals(123, $appointment->getDoctor());
        $this->assertEquals(456, $appointment->getPatient());
        $this->assertEquals(30, $appointment->getDuration());
        $this->assertEquals('Scheduled', $appointment->getStatus());
        $this->assertEquals('Annual checkup', $appointment->getReason());

        $array = $appointment->toArray();
        $this->assertEquals(123, $array['doctor']);
        $this->assertEquals(456, $array['patient']);
        $this->assertEquals(30, $array['duration']);
    }

    public function testUserModel(): void
    {
        $user = User::fromArray([
            'id' => 789,
            'username' => 'drjones',
            'first_name' => 'Sarah',
            'last_name' => 'Jones',
            'email' => 'sarah@clinic.com',
            'specialty' => 'Family Medicine',
            'npi' => '1234567890',
            'is_staff' => false,
        ]);

        $this->assertEquals(789, $user->getId());
        $this->assertEquals('drjones', $user->getUsername());
        $this->assertEquals('Sarah Jones', $user->getFullName());
        $this->assertEquals('sarah@clinic.com', $user->getEmail());
        $this->assertEquals('Family Medicine', $user->getSpecialty());
        $this->assertFalse($user->isStaff());
        $this->assertTrue($user->isDoctor());
    }

    public function testJsonSerializable(): void
    {
        $patient = new Patient();
        $patient->setFirstName('Test')
            ->setLastName('User')
            ->setEmail('test@example.com');

        $json = json_encode($patient);
        $decoded = json_decode($json, true);

        $this->assertIsArray($decoded);
        $this->assertEquals('Test', $decoded['first_name']);
        $this->assertEquals('User', $decoded['last_name']);
        $this->assertEquals('test@example.com', $decoded['email']);
    }
}

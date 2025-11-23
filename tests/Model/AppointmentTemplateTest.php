<?php

declare(strict_types=1);

namespace DrChrono\Tests\Model;

use DrChrono\Model\AppointmentTemplate;
use PHPUnit\Framework\TestCase;

class AppointmentTemplateTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'doctor' => 123,
            'office' => 456,
            'profile' => 789,
            'day_of_week' => 1,
            'start_time' => '09:00',
            'duration' => 60,
        ];

        $model = AppointmentTemplate::fromArray($data);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals(123, $model->getDoctorId());
        $this->assertEquals(456, $model->getOfficeId());
        $this->assertEquals(789, $model->getProfileId());
        $this->assertEquals(1, $model->getDayOfWeek());
        $this->assertEquals('09:00', $model->getStartTime());
        $this->assertEquals(60, $model->getDuration());
    }

    public function testToArray(): void
    {
        $model = new AppointmentTemplate();
        $model->setDoctor(123)
              ->setOffice(456)
              ->setProfile(789)
              ->setDayOfWeek(2)
              ->setStartTime('10:00')
              ->setDuration(30);

        $array = $model->toArray();

        $this->assertEquals(123, $array['doctor']);
        $this->assertEquals(456, $array['office']);
        $this->assertEquals(789, $array['profile']);
        $this->assertEquals(2, $array['day_of_week']);
        $this->assertEquals('10:00', $array['start_time']);
        $this->assertEquals(30, $array['duration']);
    }

    public function testChaining(): void
    {
        $model = (new AppointmentTemplate())
            ->setDoctor(111)
            ->setOffice(222)
            ->setProfile(333)
            ->setDayOfWeek(3)
            ->setStartTime('14:00')
            ->setDuration(45);

        $this->assertEquals(111, $model->getDoctorId());
        $this->assertEquals(222, $model->getOfficeId());
        $this->assertEquals(333, $model->getProfileId());
        $this->assertEquals(3, $model->getDayOfWeek());
        $this->assertEquals('14:00', $model->getStartTime());
        $this->assertEquals(45, $model->getDuration());
    }

    public function testNullDefaults(): void
    {
        $model = new AppointmentTemplate();

        $this->assertNull($model->getId());
        $this->assertNull($model->getDoctorId());
        $this->assertNull($model->getOfficeId());
        $this->assertNull($model->getProfileId());
        $this->assertNull($model->getDayOfWeek());
        $this->assertNull($model->getStartTime());
        $this->assertNull($model->getDuration());
    }

    public function testSnakeCaseConversion(): void
    {
        $data = [
            'day_of_week' => 5,
            'start_time' => '16:00',
        ];

        $model = AppointmentTemplate::fromArray($data);

        $this->assertEquals(5, $model->getDayOfWeek());
        $this->assertEquals('16:00', $model->getStartTime());
    }
}

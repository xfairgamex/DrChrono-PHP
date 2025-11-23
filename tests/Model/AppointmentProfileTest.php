<?php

declare(strict_types=1);

namespace DrChrono\Tests\Model;

use DrChrono\Model\AppointmentProfile;
use PHPUnit\Framework\TestCase;

class AppointmentProfileTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'name' => 'New Patient Visit',
            'duration' => 60,
            'doctor' => 123,
            'color' => '#4A90E2',
            'archived' => false,
            'online_scheduling_enabled' => true,
            'sort_order' => 1,
        ];

        $model = AppointmentProfile::fromArray($data);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals('New Patient Visit', $model->getName());
        $this->assertEquals(60, $model->getDuration());
        $this->assertEquals(123, $model->getDoctorId());
        $this->assertEquals('#4A90E2', $model->getColor());
        $this->assertFalse($model->isArchived());
        $this->assertTrue($model->isOnlineBookable());
        $this->assertEquals(1, $model->getSortOrder());
    }

    public function testToArray(): void
    {
        $model = new AppointmentProfile();
        $model->setName('Follow-up Visit')
              ->setDuration(30)
              ->setDoctor(456)
              ->setColor('#FF5733')
              ->setArchived(true)
              ->setSortOrder(2);

        $array = $model->toArray();

        $this->assertEquals('Follow-up Visit', $array['name']);
        $this->assertEquals(30, $array['duration']);
        $this->assertEquals(456, $array['doctor']);
        $this->assertEquals('#FF5733', $array['color']);
        $this->assertTrue($array['archived']);
        $this->assertEquals(2, $array['sort_order']);
    }

    public function testChaining(): void
    {
        $model = (new AppointmentProfile())
            ->setName('Telehealth')
            ->setDuration(20)
            ->setDoctor(789)
            ->setColor('#00FF00');

        $this->assertEquals('Telehealth', $model->getName());
        $this->assertEquals(20, $model->getDuration());
        $this->assertEquals(789, $model->getDoctorId());
        $this->assertEquals('#00FF00', $model->getColor());
    }

    public function testNullDefaults(): void
    {
        $model = new AppointmentProfile();

        $this->assertNull($model->getId());
        $this->assertNull($model->getName());
        $this->assertNull($model->getDuration());
        $this->assertNull($model->getDoctorId());
        $this->assertNull($model->getColor());
        $this->assertFalse($model->isArchived());
        $this->assertFalse($model->isOnlineBookable());
    }

    public function testIsArchived(): void
    {
        $model = new AppointmentProfile();
        $this->assertFalse($model->isArchived());

        $model->setArchived(true);
        $this->assertTrue($model->isArchived());
    }

    public function testIsOnlineBookable(): void
    {
        $model = new AppointmentProfile();
        $this->assertFalse($model->isOnlineBookable());

        $model->setOnlineSchedulingEnabled(true);
        $this->assertTrue($model->isOnlineBookable());
    }

    public function testSnakeCaseConversion(): void
    {
        $data = [
            'online_scheduling_enabled' => true,
            'sort_order' => 5,
        ];

        $model = AppointmentProfile::fromArray($data);

        $this->assertTrue($model->isOnlineBookable());
        $this->assertEquals(5, $model->getSortOrder());
    }
}

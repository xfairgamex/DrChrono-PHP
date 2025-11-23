<?php

declare(strict_types=1);

namespace DrChrono\Tests\Model;

use DrChrono\Model\PatientFlagType;
use PHPUnit\Framework\TestCase;

class PatientFlagTypeTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'name' => 'VIP',
            'color' => '#FF0000',
            'priority' => 1,
            'doctor' => 123,
        ];

        $model = PatientFlagType::fromArray($data);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals('VIP', $model->getName());
        $this->assertEquals('#FF0000', $model->getColor());
        $this->assertEquals(1, $model->getPriority());
        $this->assertEquals(123, $model->getDoctorId());
    }

    public function testToArray(): void
    {
        $model = new PatientFlagType();
        $model->setName('High Risk')
              ->setColor('#FFA500')
              ->setPriority(2)
              ->setDoctor(456);

        $array = $model->toArray();

        $this->assertEquals('High Risk', $array['name']);
        $this->assertEquals('#FFA500', $array['color']);
        $this->assertEquals(2, $array['priority']);
        $this->assertEquals(456, $array['doctor']);
    }

    public function testChaining(): void
    {
        $model = (new PatientFlagType())
            ->setName('Special Care')
            ->setColor('#00FF00')
            ->setPriority(3)
            ->setDoctor(789);

        $this->assertEquals('Special Care', $model->getName());
        $this->assertEquals('#00FF00', $model->getColor());
        $this->assertEquals(3, $model->getPriority());
        $this->assertEquals(789, $model->getDoctorId());
    }

    public function testNullDefaults(): void
    {
        $model = new PatientFlagType();

        $this->assertNull($model->getId());
        $this->assertNull($model->getName());
        $this->assertNull($model->getColor());
        $this->assertNull($model->getPriority());
        $this->assertNull($model->getDoctorId());
    }
}

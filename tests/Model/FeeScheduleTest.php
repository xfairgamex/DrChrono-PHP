<?php

declare(strict_types=1);

namespace DrChrono\Tests\Model;

use DrChrono\Model\FeeSchedule;
use PHPUnit\Framework\TestCase;

class FeeScheduleTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Standard Fee Schedule',
            'code' => '99213',
            'price' => 150.00,
            'doctor' => 123,
            'insurance_plan' => 456,
        ];

        $model = FeeSchedule::fromArray($data);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals('Standard Fee Schedule', $model->getName());
        $this->assertEquals('99213', $model->getCode());
        $this->assertEquals(150.00, $model->getPrice());
        $this->assertEquals(123, $model->getDoctorId());
        $this->assertEquals(456, $model->getInsurancePlanId());
    }

    public function testToArray(): void
    {
        $model = new FeeSchedule();
        $model->setName('Test Schedule')
              ->setCode('99214')
              ->setPrice(200.00)
              ->setDoctor(123);

        $array = $model->toArray();

        $this->assertEquals('Test Schedule', $array['name']);
        $this->assertEquals('99214', $array['code']);
        $this->assertEquals(200.00, $array['price']);
        $this->assertEquals(123, $array['doctor']);
    }

    public function testChaining(): void
    {
        $model = (new FeeSchedule())
            ->setName('Test')
            ->setCode('99213')
            ->setPrice(150.00);

        $this->assertEquals('Test', $model->getName());
        $this->assertEquals('99213', $model->getCode());
        $this->assertEquals(150.00, $model->getPrice());
    }

    public function testNullDefaults(): void
    {
        $model = new FeeSchedule();

        $this->assertNull($model->getId());
        $this->assertNull($model->getName());
        $this->assertNull($model->getCode());
        $this->assertNull($model->getPrice());
        $this->assertNull($model->getDoctorId());
    }

    public function testModifiersHandling(): void
    {
        $modifiers = ['26', 'TC'];
        $model = new FeeSchedule();
        $model->setModifiers($modifiers);

        $this->assertEquals($modifiers, $model->getModifiers());
    }
}

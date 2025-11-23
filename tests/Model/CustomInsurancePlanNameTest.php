<?php

declare(strict_types=1);

namespace DrChrono\Tests\Model;

use DrChrono\Model\CustomInsurancePlanName;
use PHPUnit\Framework\TestCase;

class CustomInsurancePlanNameTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'insurance_plan' => 123,
            'custom_name' => 'My Custom Plan Name',
            'doctor' => 456,
            'notes' => 'Custom naming for clarity',
        ];

        $model = CustomInsurancePlanName::fromArray($data);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals(123, $model->getInsurancePlanId());
        $this->assertEquals('My Custom Plan Name', $model->getCustomName());
        $this->assertEquals(456, $model->getDoctorId());
        $this->assertEquals('Custom naming for clarity', $model->getNotes());
    }

    public function testToArray(): void
    {
        $model = new CustomInsurancePlanName();
        $model->setInsurancePlan(123)
              ->setCustomName('Test Custom Name')
              ->setDoctor(456);

        $array = $model->toArray();

        $this->assertEquals(123, $array['insurance_plan']);
        $this->assertEquals('Test Custom Name', $array['custom_name']);
        $this->assertEquals(456, $array['doctor']);
    }

    public function testChaining(): void
    {
        $model = (new CustomInsurancePlanName())
            ->setInsurancePlan(123)
            ->setCustomName('Custom Plan')
            ->setNotes('Test notes');

        $this->assertEquals(123, $model->getInsurancePlanId());
        $this->assertEquals('Custom Plan', $model->getCustomName());
        $this->assertEquals('Test notes', $model->getNotes());
    }

    public function testNullDefaults(): void
    {
        $model = new CustomInsurancePlanName();

        $this->assertNull($model->getId());
        $this->assertNull($model->getInsurancePlanId());
        $this->assertNull($model->getCustomName());
        $this->assertNull($model->getDoctorId());
        $this->assertNull($model->getNotes());
    }
}

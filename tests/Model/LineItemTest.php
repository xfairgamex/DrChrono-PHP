<?php

declare(strict_types=1);

namespace DrChrono\Tests\Model;

use DrChrono\Model\LineItem;
use PHPUnit\Framework\TestCase;

class LineItemTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'appointment' => 789,
            'code' => '99213',
            'procedure_type' => 'CPT',
            'quantity' => 1,
            'price' => 150.00,
            'adjustment' => 10.00,
            'doctor' => 123,
        ];

        $model = LineItem::fromArray($data);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals(789, $model->getAppointmentId());
        $this->assertEquals('99213', $model->getCode());
        $this->assertEquals('CPT', $model->getProcedureType());
        $this->assertEquals(1, $model->getQuantity());
        $this->assertEquals(150.00, $model->getPrice());
        $this->assertEquals(10.00, $model->getAdjustment());
        $this->assertEquals(123, $model->getDoctorId());
    }

    public function testToArray(): void
    {
        $model = new LineItem();
        $model->setAppointment(789)
              ->setCode('99214')
              ->setProcedureType('CPT')
              ->setPrice(200.00);

        $array = $model->toArray();

        $this->assertEquals(789, $array['appointment']);
        $this->assertEquals('99214', $array['code']);
        $this->assertEquals('CPT', $array['procedure_type']);
        $this->assertEquals(200.00, $array['price']);
    }

    public function testChaining(): void
    {
        $model = (new LineItem())
            ->setCode('99213')
            ->setPrice(150.00)
            ->setQuantity(2);

        $this->assertEquals('99213', $model->getCode());
        $this->assertEquals(150.00, $model->getPrice());
        $this->assertEquals(2, $model->getQuantity());
    }

    public function testNullDefaults(): void
    {
        $model = new LineItem();

        $this->assertNull($model->getId());
        $this->assertNull($model->getAppointmentId());
        $this->assertNull($model->getCode());
        $this->assertNull($model->getPrice());
    }

    public function testGetTotal(): void
    {
        $model = new LineItem();
        $model->setPrice(100.00)
              ->setQuantity(2)
              ->setAdjustment(20.00);

        // (100 * 2) - 20 = 180
        $this->assertEquals(180.00, $model->getTotal());
    }

    public function testGetTotalWithDefaults(): void
    {
        $model = new LineItem();
        $model->setPrice(100.00);

        // 100 * 1 (default quantity) - 0 (default adjustment) = 100
        $this->assertEquals(100.00, $model->getTotal());
    }

    public function testModifiersAndDiagnosisPointers(): void
    {
        $modifiers = ['26', 'TC'];
        $pointers = ['A', 'B'];

        $model = new LineItem();
        $model->setModifiers($modifiers)
              ->setDiagnosisPointers($pointers);

        $this->assertEquals($modifiers, $model->getModifiers());
        $this->assertEquals($pointers, $model->getDiagnosisPointers());
    }
}

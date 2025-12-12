<?php

declare(strict_types=1);

namespace DrChrono\Tests\Model;

use DrChrono\Model\ConsentForm;
use PHPUnit\Framework\TestCase;

class ConsentFormTest extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'patient' => 456,
            'title' => 'HIPAA Consent',
            'content' => 'I consent to...',
            'doctor' => 123,
            'signed_date' => '2025-11-23',
            'document' => 789,
            'is_signed' => true,
        ];

        $model = ConsentForm::fromArray($data);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals(456, $model->getPatientId());
        $this->assertEquals('HIPAA Consent', $model->getTitle());
        $this->assertEquals('I consent to...', $model->getContent());
        $this->assertEquals(123, $model->getDoctorId());
        $this->assertEquals('2025-11-23', $model->getSignedDate());
        $this->assertEquals(789, $model->getDocumentId());
        $this->assertTrue($model->isSigned());
    }

    public function testToArray(): void
    {
        $model = new ConsentForm();
        $model->setPatient(456)
              ->setTitle('Treatment Consent')
              ->setContent('I agree to treatment')
              ->setSigned(true);

        $array = $model->toArray();

        $this->assertEquals(456, $array['patient']);
        $this->assertEquals('Treatment Consent', $array['title']);
        $this->assertEquals('I agree to treatment', $array['content']);
        $this->assertTrue($array['is_signed']);
    }

    public function testChaining(): void
    {
        $model = (new ConsentForm())
            ->setTitle('Test Consent')
            ->setPatient(456)
            ->setSigned(false);

        $this->assertEquals('Test Consent', $model->getTitle());
        $this->assertEquals(456, $model->getPatientId());
        $this->assertFalse($model->isSigned());
    }

    public function testNullDefaults(): void
    {
        $model = new ConsentForm();

        $this->assertNull($model->getId());
        $this->assertNull($model->getPatientId());
        $this->assertNull($model->getTitle());
        $this->assertNull($model->getContent());
    }

    public function testIsSigned(): void
    {
        $model = new ConsentForm();
        $this->assertFalse($model->isSigned());

        $model->setSigned(true);
        $this->assertTrue($model->isSigned());
    }

    public function testRequiresSignature(): void
    {
        $model = new ConsentForm();
        $this->assertTrue($model->requiresSignature());

        $model->setSigned(true);
        $this->assertFalse($model->requiresSignature());
    }
}

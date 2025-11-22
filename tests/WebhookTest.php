<?php

declare(strict_types=1);

namespace DrChrono\Tests;

use DrChrono\Webhook\WebhookVerifier;
use DrChrono\Webhook\WebhookEvent;
use DrChrono\Exception\WebhookVerificationException;
use PHPUnit\Framework\TestCase;

class WebhookTest extends TestCase
{
    private string $secret = 'test_secret_key';

    public function testVerifyValidSignature(): void
    {
        $verifier = new WebhookVerifier($this->secret);
        $payload = 'test payload data';
        $signature = hash_hmac('sha256', $payload, $this->secret);

        $result = $verifier->verify($payload, $signature);

        $this->assertTrue($result);
    }

    public function testVerifyInvalidSignature(): void
    {
        $verifier = new WebhookVerifier($this->secret);
        $payload = 'test payload data';
        $signature = 'invalid_signature';

        $result = $verifier->verify($payload, $signature);

        $this->assertFalse($result);
    }

    public function testVerifyAndParseValidPayload(): void
    {
        $verifier = new WebhookVerifier($this->secret);
        $data = ['event' => 'patient.created', 'patient_id' => 123];
        $payload = json_encode($data);
        $signature = hash_hmac('sha256', $payload, $this->secret);

        $event = $verifier->verifyAndParse($payload, $signature);

        $this->assertInstanceOf(WebhookEvent::class, $event);
        $this->assertEquals('patient.created', $event->getEvent());
    }

    public function testVerifyAndParseInvalidSignature(): void
    {
        $this->expectException(WebhookVerificationException::class);

        $verifier = new WebhookVerifier($this->secret);
        $payload = json_encode(['event' => 'test']);
        $signature = 'invalid';

        $verifier->verifyAndParse($payload, $signature);
    }

    public function testVerifyAndParseInvalidJson(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('Invalid JSON payload');

        $verifier = new WebhookVerifier($this->secret);
        $payload = 'not valid json';
        $signature = hash_hmac('sha256', $payload, $this->secret);

        $verifier->verifyAndParse($payload, $signature);
    }

    public function testWebhookEventCreation(): void
    {
        $event = new WebhookEvent('appointment.created', [
            'appointment_id' => 456,
            'patient_id' => 789,
        ], 'appointment');

        $this->assertEquals('appointment.created', $event->getEvent());
        $this->assertEquals('appointment', $event->getObject());
        $this->assertEquals(456, $event->getAppointmentId());
        $this->assertEquals(789, $event->getPatientId());
    }

    public function testWebhookEventFromArray(): void
    {
        $data = [
            'event' => 'patient.updated',
            'object' => 'patient',
            'data' => [
                'patient_id' => 123,
                'first_name' => 'John',
                'last_name' => 'Doe',
            ],
        ];

        $event = WebhookEvent::fromArray($data);

        $this->assertEquals('patient.updated', $event->getEvent());
        $this->assertEquals('patient', $event->getObject());
        $this->assertTrue($event->isPatientEvent());
    }

    public function testWebhookEventTypeChecking(): void
    {
        $event = new WebhookEvent('appointment.created', [
            'appointment_id' => 123,
        ], 'appointment');

        $this->assertTrue($event->is('appointment.created'));
        $this->assertFalse($event->is('patient.created'));
        $this->assertTrue($event->isObjectType('appointment'));
        $this->assertTrue($event->isAppointmentEvent());
        $this->assertFalse($event->isPatientEvent());
    }

    public function testGenerateSignature(): void
    {
        $verifier = new WebhookVerifier($this->secret);
        $payload = 'test data';

        $signature = $verifier->generateSignature($payload);

        $this->assertNotEmpty($signature);
        $this->assertTrue($verifier->verify($payload, $signature));
    }

    public function testExtractSignatureFromHeaders(): void
    {
        $verifier = new WebhookVerifier($this->secret);
        $payload = json_encode(['event' => 'test']);
        $signature = hash_hmac('sha256', $payload, $this->secret);

        $headers = [
            'X-DrChrono-Signature' => $signature,
            'Content-Type' => 'application/json',
        ];

        $event = $verifier->verifyFromRequest($payload, $headers, requireSignature: false);

        $this->assertInstanceOf(WebhookEvent::class, $event);
    }

    public function testMissingSignatureWhenRequired(): void
    {
        $this->expectException(WebhookVerificationException::class);
        $this->expectExceptionMessage('Missing webhook signature');

        $verifier = new WebhookVerifier($this->secret);
        $payload = json_encode(['event' => 'test']);

        $verifier->verifyFromRequest($payload, [], requireSignature: true);
    }
}

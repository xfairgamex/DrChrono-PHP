<?php

declare(strict_types=1);

namespace DrChrono\Webhook;

/**
 * Webhook event data container
 */
class WebhookEvent
{
    private string $event;
    private ?string $object = null;
    private array $data;
    private ?string $receivedAt = null;

    public function __construct(string $event, array $data = [], ?string $object = null)
    {
        $this->event = $event;
        $this->data = $data;
        $this->object = $object;
        $this->receivedAt = date('c');
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['event'] ?? 'unknown',
            $data['data'] ?? $data,
            $data['object'] ?? null
        );
    }

    public function getEvent(): string
    {
        return $this->event;
    }

    public function getObject(): ?string
    {
        return $this->object;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getReceivedAt(): ?string
    {
        return $this->receivedAt;
    }

    /**
     * Check if this is a specific event type
     */
    public function is(string $eventType): bool
    {
        return $this->event === $eventType;
    }

    /**
     * Check if this is for a specific object type
     */
    public function isObjectType(string $objectType): bool
    {
        return $this->object === $objectType;
    }

    /**
     * Get data value by key
     */
    public function get(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Check if event is for patient
     */
    public function isPatientEvent(): bool
    {
        return $this->isObjectType('patient') || isset($this->data['patient']);
    }

    /**
     * Check if event is for appointment
     */
    public function isAppointmentEvent(): bool
    {
        return $this->isObjectType('appointment') || isset($this->data['appointment']);
    }

    /**
     * Get patient ID from event
     */
    public function getPatientId(): ?int
    {
        return $this->data['patient'] ?? $this->data['patient_id'] ?? null;
    }

    /**
     * Get appointment ID from event
     */
    public function getAppointmentId(): ?int
    {
        return $this->data['appointment'] ?? $this->data['appointment_id'] ?? null;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'event' => $this->event,
            'object' => $this->object,
            'data' => $this->data,
            'received_at' => $this->receivedAt,
        ];
    }

    /**
     * JSON serialize
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Prescription Message Model
 *
 * Represents a pharmacy communication message regarding a prescription.
 */
class PrescriptionMessage extends AbstractModel
{
    /**
     * Get message ID
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get patient ID
     */
    public function getPatientId(): ?int
    {
        return $this->data['patient'] ?? null;
    }

    /**
     * Set patient ID
     */
    public function setPatient(int $patientId): self
    {
        $this->data['patient'] = $patientId;
        return $this;
    }

    /**
     * Get doctor ID
     */
    public function getDoctorId(): ?int
    {
        return $this->data['doctor'] ?? null;
    }

    /**
     * Set doctor ID
     */
    public function setDoctor(int $doctorId): self
    {
        $this->data['doctor'] = $doctorId;
        return $this;
    }

    /**
     * Get prescription ID
     */
    public function getPrescriptionId(): ?int
    {
        return $this->data['prescription'] ?? null;
    }

    /**
     * Set prescription ID
     */
    public function setPrescription(int $prescriptionId): self
    {
        $this->data['prescription'] = $prescriptionId;
        return $this;
    }

    /**
     * Get message type
     */
    public function getMessageType(): ?string
    {
        return $this->data['message_type'] ?? null;
    }

    /**
     * Set message type
     */
    public function setMessageType(string $messageType): self
    {
        $this->data['message_type'] = $messageType;
        return $this;
    }

    /**
     * Get message content
     */
    public function getContent(): ?string
    {
        return $this->data['content'] ?? null;
    }

    /**
     * Set message content
     */
    public function setContent(string $content): self
    {
        $this->data['content'] = $content;
        return $this;
    }

    /**
     * Get status
     */
    public function getStatus(): ?string
    {
        return $this->data['status'] ?? null;
    }

    /**
     * Set status
     */
    public function setStatus(string $status): self
    {
        $this->data['status'] = $status;
        return $this;
    }

    /**
     * Get pharmacy ID
     */
    public function getPharmacyId(): ?string
    {
        return $this->data['pharmacy_id'] ?? null;
    }

    /**
     * Set pharmacy ID
     */
    public function setPharmacyId(string $pharmacyId): self
    {
        $this->data['pharmacy_id'] = $pharmacyId;
        return $this;
    }

    /**
     * Get response content
     */
    public function getResponse(): ?string
    {
        return $this->data['response'] ?? null;
    }

    /**
     * Set response content
     */
    public function setResponse(string $response): self
    {
        $this->data['response'] = $response;
        return $this;
    }

    /**
     * Check if message is read
     */
    public function isRead(): bool
    {
        return $this->data['is_read'] ?? false;
    }

    /**
     * Set read status
     */
    public function setRead(bool $isRead): self
    {
        $this->data['is_read'] = $isRead;
        return $this;
    }

    /**
     * Get created timestamp
     */
    public function getCreatedAt(): ?string
    {
        return $this->data['created_at'] ?? null;
    }

    /**
     * Get updated timestamp
     */
    public function getUpdatedAt(): ?string
    {
        return $this->data['updated_at'] ?? null;
    }

    /**
     * Check if this is a refill request
     */
    public function isRefillRequest(): bool
    {
        return ($this->getMessageType() ?? '') === 'refill_request';
    }

    /**
     * Check if this is a new prescription
     */
    public function isNewRx(): bool
    {
        return ($this->getMessageType() ?? '') === 'new_rx';
    }

    /**
     * Check if this is a change request
     */
    public function isChangeRequest(): bool
    {
        return ($this->getMessageType() ?? '') === 'change_request';
    }

    /**
     * Check if message is pending
     */
    public function isPending(): bool
    {
        return ($this->getStatus() ?? '') === 'pending';
    }

    /**
     * Check if message has been sent
     */
    public function isSent(): bool
    {
        return in_array($this->getStatus() ?? '', ['sent', 'delivered', 'responded']);
    }

    /**
     * Check if message has been responded to
     */
    public function isResponded(): bool
    {
        return ($this->getStatus() ?? '') === 'responded';
    }

    /**
     * Check if message failed
     */
    public function isFailed(): bool
    {
        return ($this->getStatus() ?? '') === 'failed';
    }
}

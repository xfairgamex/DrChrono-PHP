<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Consent Form Model
 *
 * Represents a patient consent or authorization form
 */
class ConsentForm extends AbstractModel
{
    /**
     * Get consent form ID
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
     * Get form title
     */
    public function getTitle(): ?string
    {
        return $this->data['title'] ?? null;
    }

    /**
     * Set form title
     */
    public function setTitle(string $title): self
    {
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * Get form content
     */
    public function getContent(): ?string
    {
        return $this->data['content'] ?? null;
    }

    /**
     * Set form content
     */
    public function setContent(string $content): self
    {
        $this->data['content'] = $content;
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
     * Get signed date
     */
    public function getSignedDate(): ?string
    {
        return $this->data['signed_date'] ?? null;
    }

    /**
     * Set signed date
     */
    public function setSignedDate(string $date): self
    {
        $this->data['signed_date'] = $date;
        return $this;
    }

    /**
     * Get associated document ID
     */
    public function getDocumentId(): ?int
    {
        return $this->data['document'] ?? null;
    }

    /**
     * Set associated document ID
     */
    public function setDocument(int $documentId): self
    {
        $this->data['document'] = $documentId;
        return $this;
    }

    /**
     * Check if form is signed
     */
    public function isSigned(): bool
    {
        return !empty($this->data['is_signed']);
    }

    /**
     * Set signed status
     */
    public function setSigned(bool $signed): self
    {
        $this->data['is_signed'] = $signed;
        return $this;
    }

    /**
     * Check if form requires signature
     */
    public function requiresSignature(): bool
    {
        return !$this->isSigned();
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
}

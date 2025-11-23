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
    protected ?int $id = null;
    protected ?int $patient = null;
    protected ?string $title = null;
    protected ?string $content = null;
    protected ?int $doctor = null;
    protected ?string $signedDate = null;
    protected ?int $document = null;
    protected ?bool $isSigned = null;
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;

    /**
     * Get consent form ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get patient ID
     */
    public function getPatientId(): ?int
    {
        return $this->patient;
    }

    /**
     * Set patient ID
     */
    public function setPatient(int $patientId): self
    {
        $this->patient = $patientId;
        return $this;
    }

    /**
     * Get form title
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set form title
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get form content
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * Set form content
     */
    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get doctor ID
     */
    public function getDoctorId(): ?int
    {
        return $this->doctor;
    }

    /**
     * Set doctor ID
     */
    public function setDoctor(int $doctorId): self
    {
        $this->doctor = $doctorId;
        return $this;
    }

    /**
     * Get signed date
     */
    public function getSignedDate(): ?string
    {
        return $this->signedDate;
    }

    /**
     * Set signed date
     */
    public function setSignedDate(string $date): self
    {
        $this->signedDate = $date;
        return $this;
    }

    /**
     * Get associated document ID
     */
    public function getDocumentId(): ?int
    {
        return $this->document;
    }

    /**
     * Set associated document ID
     */
    public function setDocument(int $documentId): self
    {
        $this->document = $documentId;
        return $this;
    }

    /**
     * Check if form is signed
     */
    public function isSigned(): bool
    {
        return $this->isSigned ?? false;
    }

    /**
     * Set signed status
     */
    public function setSigned(bool $signed): self
    {
        $this->isSigned = $signed;
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
        return $this->createdAt;
    }

    /**
     * Get updated timestamp
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Patient Message Model
 *
 * Represents a message between patient and provider
 */
class PatientMessage extends AbstractModel
{
    protected ?int $id = null;
    protected ?int $patient = null;
    protected ?int $doctor = null;
    protected ?string $message = null;
    protected ?bool $read = null;
    protected ?string $sentAt = null;
    protected ?string $updatedAt = null;
    protected ?string $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatientId(): ?int
    {
        return $this->patient;
    }

    public function setPatient(int $patientId): self
    {
        $this->patient = $patientId;
        return $this;
    }

    public function getDoctorId(): ?int
    {
        return $this->doctor;
    }

    public function setDoctor(int $doctorId): self
    {
        $this->doctor = $doctorId;
        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function isRead(): bool
    {
        return $this->read ?? false;
    }

    public function setRead(bool $read): self
    {
        $this->read = $read;
        return $this;
    }

    public function getSentAt(): ?string
    {
        return $this->sentAt;
    }

    public function setSentAt(string $sentAt): self
    {
        $this->sentAt = $sentAt;
        return $this;
    }
}

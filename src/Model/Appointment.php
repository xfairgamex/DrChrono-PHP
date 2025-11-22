<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Appointment model
 */
class Appointment extends AbstractModel
{
    protected ?int $id = null;
    protected ?int $doctor = null;
    protected ?int $patient = null;
    protected ?int $office = null;
    protected ?int $examRoom = null;
    protected ?int $duration = null;
    protected ?string $scheduledTime = null;
    protected ?string $status = null;
    protected ?string $appointmentProfile = null;
    protected ?string $notes = null;
    protected ?string $reason = null;
    protected ?string $color = null;
    protected ?bool $allowOverlapping = null;
    protected ?bool $icdCodes = null;
    protected ?string $billingStatus = null;
    protected ?string $clinicalNoteUrl = null;
    protected ?string $updatedAt = null;
    protected ?string $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDoctor(): ?int
    {
        return $this->doctor;
    }

    public function setDoctor(int $doctor): self
    {
        $this->doctor = $doctor;
        return $this;
    }

    public function getPatient(): ?int
    {
        return $this->patient;
    }

    public function setPatient(int $patient): self
    {
        $this->patient = $patient;
        return $this;
    }

    public function getOffice(): ?int
    {
        return $this->office;
    }

    public function setOffice(int $office): self
    {
        $this->office = $office;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;
        return $this;
    }

    public function getScheduledTime(): ?string
    {
        return $this->scheduledTime;
    }

    public function setScheduledTime(string $scheduledTime): self
    {
        $this->scheduledTime = $scheduledTime;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;
        return $this;
    }
}

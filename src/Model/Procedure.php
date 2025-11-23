<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Procedure Model
 *
 * Represents a medical procedure performed on a patient
 */
class Procedure extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    public function getPatientId(): ?int
    {
        return $this->data['patient'] ?? null;
    }

    public function setPatient(int $patientId): self
    {
        $this->data['patient'] = $patientId;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->data['code'] ?? null;
    }

    public function setCode(string $code): self
    {
        $this->data['code'] = $code;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->data['description'] ?? null;
    }

    public function setDescription(string $description): self
    {
        $this->data['description'] = $description;
        return $this;
    }

    public function getDate(): ?string
    {
        return $this->data['date'] ?? null;
    }

    public function setDate(string $date): self
    {
        $this->data['date'] = $date;
        return $this;
    }

    public function getDoctorId(): ?int
    {
        return $this->data['doctor'] ?? null;
    }

    public function setDoctor(int $doctorId): self
    {
        $this->data['doctor'] = $doctorId;
        return $this;
    }

    public function getAppointmentId(): ?int
    {
        return $this->data['appointment'] ?? null;
    }

    public function setAppointment(int $appointmentId): self
    {
        $this->data['appointment'] = $appointmentId;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->data['notes'] ?? null;
    }

    public function setNotes(string $notes): self
    {
        $this->data['notes'] = $notes;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->data['status'] ?? null;
    }

    public function setStatus(string $status): self
    {
        $this->data['status'] = $status;
        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->data['created_at'] ?? null;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->data['updated_at'] ?? null;
    }
}

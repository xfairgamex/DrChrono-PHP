<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Care Plan Model
 *
 * Represents a patient care plan with treatment strategies and goals
 */
class CarePlan extends AbstractModel
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

    public function getTitle(): ?string
    {
        return $this->data['title'] ?? null;
    }

    public function setTitle(string $title): self
    {
        $this->data['title'] = $title;
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

    public function getDoctorId(): ?int
    {
        return $this->data['doctor'] ?? null;
    }

    public function setDoctor(int $doctorId): self
    {
        $this->data['doctor'] = $doctorId;
        return $this;
    }

    public function getGoals(): ?array
    {
        return $this->data['goals'] ?? null;
    }

    public function setGoals(array $goals): self
    {
        $this->data['goals'] = $goals;
        return $this;
    }

    public function getInterventions(): ?array
    {
        return $this->data['interventions'] ?? null;
    }

    public function setInterventions(array $interventions): self
    {
        $this->data['interventions'] = $interventions;
        return $this;
    }

    public function getStartDate(): ?string
    {
        return $this->data['start_date'] ?? null;
    }

    public function setStartDate(string $date): self
    {
        $this->data['start_date'] = $date;
        return $this;
    }

    public function getEndDate(): ?string
    {
        return $this->data['end_date'] ?? null;
    }

    public function setEndDate(string $date): self
    {
        $this->data['end_date'] = $date;
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

    public function isActive(): bool
    {
        return ($this->data['status'] ?? '') === 'active';
    }

    public function isCompleted(): bool
    {
        return ($this->data['status'] ?? '') === 'completed';
    }

    public function isCancelled(): bool
    {
        return ($this->data['status'] ?? '') === 'cancelled';
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

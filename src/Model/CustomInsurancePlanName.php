<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Custom Insurance Plan Name Model
 *
 * Represents a custom display name for an insurance plan
 */
class CustomInsurancePlanName extends AbstractModel
{
    /**
     * Get custom plan name ID
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get original insurance plan ID
     */
    public function getInsurancePlanId(): ?int
    {
        return $this->data['insurance_plan'] ?? null;
    }

    /**
     * Set insurance plan ID
     */
    public function setInsurancePlan(int $insurancePlanId): self
    {
        $this->data['insurance_plan'] = $insurancePlanId;
        return $this;
    }

    /**
     * Get custom display name
     */
    public function getCustomName(): ?string
    {
        return $this->data['custom_name'] ?? null;
    }

    /**
     * Set custom display name
     */
    public function setCustomName(string $name): self
    {
        $this->data['custom_name'] = $name;
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
     * Get notes
     */
    public function getNotes(): ?string
    {
        return $this->data['notes'] ?? null;
    }

    /**
     * Set notes
     */
    public function setNotes(string $notes): self
    {
        $this->data['notes'] = $notes;
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
}

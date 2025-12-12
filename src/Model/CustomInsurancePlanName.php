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
    protected ?int $id = null;
    protected ?int $insurancePlan = null;
    protected ?string $customName = null;
    protected ?int $doctor = null;
    protected ?string $notes = null;
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;

    /**
     * Get custom plan name ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get original insurance plan ID
     */
    public function getInsurancePlanId(): ?int
    {
        return $this->insurancePlan;
    }

    /**
     * Set insurance plan ID
     */
    public function setInsurancePlan(int $insurancePlanId): self
    {
        $this->insurancePlan = $insurancePlanId;
        return $this;
    }

    /**
     * Get custom display name
     */
    public function getCustomName(): ?string
    {
        return $this->customName;
    }

    /**
     * Set custom display name
     */
    public function setCustomName(string $name): self
    {
        $this->customName = $name;
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
     * Get notes
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * Set notes
     */
    public function setNotes(string $notes): self
    {
        $this->notes = $notes;
        return $this;
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

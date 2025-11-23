<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Fee Schedule Model
 *
 * Represents pricing for procedures and services
 */
class FeeSchedule extends AbstractModel
{
    protected ?int $id = null;
    protected ?string $name = null;
    protected ?string $code = null;
    protected ?float $price = null;
    protected ?int $doctor = null;
    protected ?int $insurancePlan = null;
    protected ?array $modifiers = null;
    protected ?string $updatedAt = null;
    protected ?string $createdAt = null;

    /**
     * Get fee schedule ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get schedule name
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set schedule name
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get procedure/service code
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * Set procedure/service code
     */
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Get fee amount
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Set fee amount
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
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
     * Get insurance plan ID
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
     * Get code modifiers
     */
    public function getModifiers(): ?array
    {
        return $this->modifiers;
    }

    /**
     * Set code modifiers
     */
    public function setModifiers(array $modifiers): self
    {
        $this->modifiers = $modifiers;
        return $this;
    }

    /**
     * Get updated timestamp
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    /**
     * Get created timestamp
     */
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}

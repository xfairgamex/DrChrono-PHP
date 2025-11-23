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
    /**
     * Get fee schedule ID
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get schedule name
     */
    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    /**
     * Set schedule name
     */
    public function setName(string $name): self
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * Get procedure/service code
     */
    public function getCode(): ?string
    {
        return $this->data['code'] ?? null;
    }

    /**
     * Set procedure/service code
     */
    public function setCode(string $code): self
    {
        $this->data['code'] = $code;
        return $this;
    }

    /**
     * Get fee amount
     */
    public function getPrice(): ?float
    {
        return isset($this->data['price']) ? (float) $this->data['price'] : null;
    }

    /**
     * Set fee amount
     */
    public function setPrice(float $price): self
    {
        $this->data['price'] = $price;
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
     * Get insurance plan ID
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
     * Get code modifiers
     */
    public function getModifiers(): ?array
    {
        return $this->data['modifiers'] ?? null;
    }

    /**
     * Set code modifiers
     */
    public function setModifiers(array $modifiers): self
    {
        $this->data['modifiers'] = $modifiers;
        return $this;
    }

    /**
     * Get updated timestamp
     */
    public function getUpdatedAt(): ?string
    {
        return $this->data['updated_at'] ?? null;
    }

    /**
     * Get created timestamp
     */
    public function getCreatedAt(): ?string
    {
        return $this->data['created_at'] ?? null;
    }
}

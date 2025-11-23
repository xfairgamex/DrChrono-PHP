<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Line Item Model
 *
 * Represents a billable procedure or service on an appointment
 */
class LineItem extends AbstractModel
{
    /**
     * Get line item ID
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get appointment ID
     */
    public function getAppointmentId(): ?int
    {
        return $this->data['appointment'] ?? null;
    }

    /**
     * Set appointment ID
     */
    public function setAppointment(int $appointmentId): self
    {
        $this->data['appointment'] = $appointmentId;
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
     * Get procedure type
     */
    public function getProcedureType(): ?string
    {
        return $this->data['procedure_type'] ?? null;
    }

    /**
     * Set procedure type
     */
    public function setProcedureType(string $type): self
    {
        $this->data['procedure_type'] = $type;
        return $this;
    }

    /**
     * Get quantity/units
     */
    public function getQuantity(): ?int
    {
        return $this->data['quantity'] ?? null;
    }

    /**
     * Set quantity/units
     */
    public function setQuantity(int $quantity): self
    {
        $this->data['quantity'] = $quantity;
        return $this;
    }

    /**
     * Get unit price
     */
    public function getPrice(): ?float
    {
        return isset($this->data['price']) ? (float) $this->data['price'] : null;
    }

    /**
     * Set unit price
     */
    public function setPrice(float $price): self
    {
        $this->data['price'] = $price;
        return $this;
    }

    /**
     * Get adjustment amount
     */
    public function getAdjustment(): ?float
    {
        return isset($this->data['adjustment']) ? (float) $this->data['adjustment'] : null;
    }

    /**
     * Set adjustment amount
     */
    public function setAdjustment(float $adjustment): self
    {
        $this->data['adjustment'] = $adjustment;
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
     * Get diagnosis pointers
     */
    public function getDiagnosisPointers(): ?array
    {
        return $this->data['diagnosis_pointers'] ?? null;
    }

    /**
     * Set diagnosis pointers
     */
    public function setDiagnosisPointers(array $pointers): self
    {
        $this->data['diagnosis_pointers'] = $pointers;
        return $this;
    }

    /**
     * Get units type
     */
    public function getUnits(): ?string
    {
        return $this->data['units'] ?? null;
    }

    /**
     * Set units type
     */
    public function setUnits(string $units): self
    {
        $this->data['units'] = $units;
        return $this;
    }

    /**
     * Get place of service code
     */
    public function getPlaceOfService(): ?int
    {
        return $this->data['place_of_service'] ?? null;
    }

    /**
     * Set place of service code
     */
    public function setPlaceOfService(int $code): self
    {
        $this->data['place_of_service'] = $code;
        return $this;
    }

    /**
     * Calculate total amount (price * quantity - adjustment)
     */
    public function getTotal(): float
    {
        $price = $this->getPrice() ?? 0.0;
        $quantity = $this->getQuantity() ?? 1;
        $adjustment = $this->getAdjustment() ?? 0.0;

        return ($price * $quantity) - $adjustment;
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

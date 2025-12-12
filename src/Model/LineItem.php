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
    protected ?int $id = null;
    protected ?int $appointment = null;
    protected ?string $code = null;
    protected ?string $procedureType = null;
    protected ?int $quantity = null;
    protected ?float $price = null;
    protected ?float $adjustment = null;
    protected ?int $doctor = null;
    protected ?array $modifiers = null;
    protected ?array $diagnosisPointers = null;
    protected ?string $units = null;
    protected ?int $placeOfService = null;
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;

    /**
     * Get line item ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get appointment ID
     */
    public function getAppointmentId(): ?int
    {
        return $this->appointment;
    }

    /**
     * Set appointment ID
     */
    public function setAppointment(int $appointmentId): self
    {
        $this->appointment = $appointmentId;
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
     * Get procedure type
     */
    public function getProcedureType(): ?string
    {
        return $this->procedureType;
    }

    /**
     * Set procedure type
     */
    public function setProcedureType(string $type): self
    {
        $this->procedureType = $type;
        return $this;
    }

    /**
     * Get quantity/units
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Set quantity/units
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Get unit price
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * Set unit price
     */
    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get adjustment amount
     */
    public function getAdjustment(): ?float
    {
        return $this->adjustment;
    }

    /**
     * Set adjustment amount
     */
    public function setAdjustment(float $adjustment): self
    {
        $this->adjustment = $adjustment;
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
     * Get diagnosis pointers
     */
    public function getDiagnosisPointers(): ?array
    {
        return $this->diagnosisPointers;
    }

    /**
     * Set diagnosis pointers
     */
    public function setDiagnosisPointers(array $pointers): self
    {
        $this->diagnosisPointers = $pointers;
        return $this;
    }

    /**
     * Get units type
     */
    public function getUnits(): ?string
    {
        return $this->units;
    }

    /**
     * Set units type
     */
    public function setUnits(string $units): self
    {
        $this->units = $units;
        return $this;
    }

    /**
     * Get place of service code
     */
    public function getPlaceOfService(): ?int
    {
        return $this->placeOfService;
    }

    /**
     * Set place of service code
     */
    public function setPlaceOfService(int $code): self
    {
        $this->placeOfService = $code;
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

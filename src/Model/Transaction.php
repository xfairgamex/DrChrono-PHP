<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Transaction Model
 *
 * Represents a payment, adjustment, or other financial transaction
 */
class Transaction extends AbstractModel
{
    protected ?int $id = null;
    protected ?int $appointment = null;
    protected ?float $amount = null;
    protected ?string $transactionType = null;
    protected ?string $postedDate = null;
    protected ?string $checkNumber = null;
    protected ?string $insName = null;
    protected ?string $note = null;
    protected ?int $doctor = null;
    protected ?string $createdAt = null;
    protected ?string $updatedAt = null;

    /**
     * Get transaction ID
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
     * Get transaction amount
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * Set transaction amount
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get transaction type
     */
    public function getTransactionType(): ?string
    {
        return $this->transactionType;
    }

    /**
     * Set transaction type
     */
    public function setTransactionType(string $type): self
    {
        $this->transactionType = $type;
        return $this;
    }

    /**
     * Get posted date
     */
    public function getPostedDate(): ?string
    {
        return $this->postedDate;
    }

    /**
     * Set posted date
     */
    public function setPostedDate(string $date): self
    {
        $this->postedDate = $date;
        return $this;
    }

    /**
     * Get check number
     */
    public function getCheckNumber(): ?string
    {
        return $this->checkNumber;
    }

    /**
     * Set check number
     */
    public function setCheckNumber(string $checkNumber): self
    {
        $this->checkNumber = $checkNumber;
        return $this;
    }

    /**
     * Get insurance company name
     */
    public function getInsuranceName(): ?string
    {
        return $this->insName;
    }

    /**
     * Set insurance company name
     */
    public function setInsuranceName(string $name): self
    {
        $this->insName = $name;
        return $this;
    }

    /**
     * Get transaction note
     */
    public function getNote(): ?string
    {
        return $this->note;
    }

    /**
     * Set transaction note
     */
    public function setNote(string $note): self
    {
        $this->note = $note;
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
     * Check if transaction is a payment
     */
    public function isPayment(): bool
    {
        return $this->getTransactionType() === 'Payment';
    }

    /**
     * Check if transaction is an adjustment
     */
    public function isAdjustment(): bool
    {
        return $this->getTransactionType() === 'Adjustment';
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

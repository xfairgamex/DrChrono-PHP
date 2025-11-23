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
    /**
     * Get transaction ID
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
     * Get transaction amount
     */
    public function getAmount(): ?float
    {
        return isset($this->data['amount']) ? (float) $this->data['amount'] : null;
    }

    /**
     * Set transaction amount
     */
    public function setAmount(float $amount): self
    {
        $this->data['amount'] = $amount;
        return $this;
    }

    /**
     * Get transaction type
     */
    public function getTransactionType(): ?string
    {
        return $this->data['transaction_type'] ?? null;
    }

    /**
     * Set transaction type
     */
    public function setTransactionType(string $type): self
    {
        $this->data['transaction_type'] = $type;
        return $this;
    }

    /**
     * Get posted date
     */
    public function getPostedDate(): ?string
    {
        return $this->data['posted_date'] ?? null;
    }

    /**
     * Set posted date
     */
    public function setPostedDate(string $date): self
    {
        $this->data['posted_date'] = $date;
        return $this;
    }

    /**
     * Get check number
     */
    public function getCheckNumber(): ?string
    {
        return $this->data['check_number'] ?? null;
    }

    /**
     * Set check number
     */
    public function setCheckNumber(string $checkNumber): self
    {
        $this->data['check_number'] = $checkNumber;
        return $this;
    }

    /**
     * Get insurance company name
     */
    public function getInsuranceName(): ?string
    {
        return $this->data['ins_name'] ?? null;
    }

    /**
     * Set insurance company name
     */
    public function setInsuranceName(string $name): self
    {
        $this->data['ins_name'] = $name;
        return $this;
    }

    /**
     * Get transaction note
     */
    public function getNote(): ?string
    {
        return $this->data['note'] ?? null;
    }

    /**
     * Set transaction note
     */
    public function setNote(string $note): self
    {
        $this->data['note'] = $note;
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

<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Eligibility Check Model
 *
 * Represents an insurance eligibility verification request and result
 */
class EligibilityCheck extends AbstractModel
{
    protected ?int $id = null;
    protected ?int $patient = null;
    protected ?int $appointment = null;
    protected ?int $doctor = null;
    protected ?string $insurance = null;
    protected ?string $serviceType = null;
    protected ?bool $isEligible = null;
    protected ?string $status = null;
    protected ?float $copayAmount = null;
    protected ?float $deductibleAmount = null;
    protected ?float $deductibleRemaining = null;
    protected ?float $oopMax = null;
    protected ?float $oopRemaining = null;
    protected ?string $errorMessage = null;
    protected ?array $rawResponse = null;
    protected ?string $checkedAt = null;
    protected ?string $updatedAt = null;
    protected ?string $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPatientId(): ?int
    {
        return $this->patient;
    }

    public function setPatient(int $patientId): self
    {
        $this->patient = $patientId;
        return $this;
    }

    public function getAppointmentId(): ?int
    {
        return $this->appointment;
    }

    public function setAppointment(int $appointmentId): self
    {
        $this->appointment = $appointmentId;
        return $this;
    }

    public function getDoctorId(): ?int
    {
        return $this->doctor;
    }

    public function setDoctor(int $doctorId): self
    {
        $this->doctor = $doctorId;
        return $this;
    }

    public function getInsurance(): ?string
    {
        return $this->insurance;
    }

    public function setInsurance(string $insurance): self
    {
        $this->insurance = $insurance;
        return $this;
    }

    public function getServiceType(): ?string
    {
        return $this->serviceType;
    }

    public function setServiceType(string $serviceType): self
    {
        $this->serviceType = $serviceType;
        return $this;
    }

    public function isEligible(): bool
    {
        return $this->isEligible ?? false;
    }

    public function setIsEligible(bool $eligible): self
    {
        $this->isEligible = $eligible;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getCopayAmount(): ?float
    {
        return $this->copayAmount;
    }

    public function setCopayAmount(float $amount): self
    {
        $this->copayAmount = $amount;
        return $this;
    }

    public function getDeductibleAmount(): ?float
    {
        return $this->deductibleAmount;
    }

    public function setDeductibleAmount(float $amount): self
    {
        $this->deductibleAmount = $amount;
        return $this;
    }

    public function getDeductibleRemaining(): ?float
    {
        return $this->deductibleRemaining;
    }

    public function setDeductibleRemaining(float $amount): self
    {
        $this->deductibleRemaining = $amount;
        return $this;
    }

    public function getOopMax(): ?float
    {
        return $this->oopMax;
    }

    public function setOopMax(float $amount): self
    {
        $this->oopMax = $amount;
        return $this;
    }

    public function getOopRemaining(): ?float
    {
        return $this->oopRemaining;
    }

    public function setOopRemaining(float $amount): self
    {
        $this->oopRemaining = $amount;
        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $message): self
    {
        $this->errorMessage = $message;
        return $this;
    }

    public function getRawResponse(): ?array
    {
        return $this->rawResponse;
    }

    public function setRawResponse(?array $response): self
    {
        $this->rawResponse = $response;
        return $this;
    }

    public function getCheckedAt(): ?string
    {
        return $this->checkedAt;
    }

    public function setCheckedAt(string $timestamp): self
    {
        $this->checkedAt = $timestamp;
        return $this;
    }

    /**
     * Check if the eligibility check has an error
     */
    public function hasError(): bool
    {
        return !empty($this->errorMessage);
    }

    /**
     * Check if the eligibility check is completed
     */
    public function isCompleted(): bool
    {
        return in_array($this->status, ['complete', 'completed', 'success'], true);
    }

    /**
     * Check if the eligibility check is pending
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['pending', 'processing', 'in_progress'], true);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Patient Payment Model
 *
 * Represents a payment made by a patient
 */
class PatientPayment extends AbstractModel
{
    protected ?int $id = null;
    protected ?int $patient = null;
    protected ?int $appointment = null;
    protected ?float $amount = null;
    protected ?string $paymentMethod = null;
    protected ?string $paymentDate = null;
    protected ?string $notes = null;
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

    public function setAppointment(?int $appointmentId): self
    {
        $this->appointment = $appointmentId;
        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function getPaymentDate(): ?string
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(string $paymentDate): self
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }
}

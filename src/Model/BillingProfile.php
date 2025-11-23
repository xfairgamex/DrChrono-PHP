<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Billing Profile Model
 *
 * Represents billing configuration for a provider or practice
 */
class BillingProfile extends AbstractModel
{
    protected ?int $id = null;
    protected ?int $doctor = null;
    protected ?string $npi = null;
    protected ?string $taxId = null;
    protected ?array $billingAddress = null;
    protected ?array $payToAddress = null;
    protected ?string $taxonomy = null;
    protected ?string $stateLicenseNumber = null;
    protected ?string $updatedAt = null;
    protected ?string $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNpi(): ?string
    {
        return $this->npi;
    }

    public function setNpi(string $npi): self
    {
        $this->npi = $npi;
        return $this;
    }

    public function getTaxId(): ?string
    {
        return $this->taxId;
    }

    public function setTaxId(string $taxId): self
    {
        $this->taxId = $taxId;
        return $this;
    }

    public function getBillingAddress(): ?array
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(array $address): self
    {
        $this->billingAddress = $address;
        return $this;
    }

    public function getPayToAddress(): ?array
    {
        return $this->payToAddress;
    }

    public function setPayToAddress(array $address): self
    {
        $this->payToAddress = $address;
        return $this;
    }

    public function getTaxonomy(): ?string
    {
        return $this->taxonomy;
    }

    public function setTaxonomy(string $taxonomy): self
    {
        $this->taxonomy = $taxonomy;
        return $this;
    }

    public function getStateLicenseNumber(): ?string
    {
        return $this->stateLicenseNumber;
    }

    public function setStateLicenseNumber(string $licenseNumber): self
    {
        $this->stateLicenseNumber = $licenseNumber;
        return $this;
    }
}

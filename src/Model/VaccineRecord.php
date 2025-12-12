<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Vaccine Record Model
 *
 * Represents a patient vaccine record for immunization tracking
 */
class VaccineRecord extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    public function getPatientId(): ?int
    {
        return $this->data['patient'] ?? null;
    }

    public function setPatient(int $patientId): self
    {
        $this->data['patient'] = $patientId;
        return $this;
    }

    public function getVaccineId(): ?int
    {
        return $this->data['vaccine'] ?? null;
    }

    public function setVaccine(int $vaccineId): self
    {
        $this->data['vaccine'] = $vaccineId;
        return $this;
    }

    public function getAdministeredAt(): ?string
    {
        return $this->data['administered_at'] ?? null;
    }

    public function setAdministeredAt(string $administeredAt): self
    {
        $this->data['administered_at'] = $administeredAt;
        return $this;
    }

    public function getDoctorId(): ?int
    {
        return $this->data['doctor'] ?? null;
    }

    public function setDoctor(int $doctorId): self
    {
        $this->data['doctor'] = $doctorId;
        return $this;
    }

    public function getDose(): ?float
    {
        return $this->data['dose'] ?? null;
    }

    public function setDose(float $dose): self
    {
        $this->data['dose'] = $dose;
        return $this;
    }

    public function getUnits(): ?string
    {
        return $this->data['units'] ?? null;
    }

    public function setUnits(string $units): self
    {
        $this->data['units'] = $units;
        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->data['route'] ?? null;
    }

    public function setRoute(string $route): self
    {
        $this->data['route'] = $route;
        return $this;
    }

    public function getSite(): ?string
    {
        return $this->data['site'] ?? null;
    }

    public function setSite(string $site): self
    {
        $this->data['site'] = $site;
        return $this;
    }

    public function getLotNumber(): ?string
    {
        return $this->data['lot_number'] ?? null;
    }

    public function setLotNumber(string $lotNumber): self
    {
        $this->data['lot_number'] = $lotNumber;
        return $this;
    }

    public function getManufacturer(): ?string
    {
        return $this->data['manufacturer'] ?? null;
    }

    public function setManufacturer(string $manufacturer): self
    {
        $this->data['manufacturer'] = $manufacturer;
        return $this;
    }

    public function getExpirationDate(): ?string
    {
        return $this->data['expiration_date'] ?? null;
    }

    public function setExpirationDate(string $expirationDate): self
    {
        $this->data['expiration_date'] = $expirationDate;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->data['notes'] ?? null;
    }

    public function setNotes(string $notes): self
    {
        $this->data['notes'] = $notes;
        return $this;
    }

    public function getVisDate(): ?string
    {
        return $this->data['vis_date'] ?? null;
    }

    public function setVisDate(string $visDate): self
    {
        $this->data['vis_date'] = $visDate;
        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->data['created_at'] ?? null;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->data['updated_at'] ?? null;
    }

    /**
     * Check if vaccine is expired
     */
    public function isExpired(): bool
    {
        $expirationDate = $this->getExpirationDate();
        if (!$expirationDate) {
            return false;
        }
        return strtotime($expirationDate) < time();
    }
}

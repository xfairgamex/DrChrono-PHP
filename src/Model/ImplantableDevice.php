<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Implantable Device Model
 *
 * Represents a medical device implanted in a patient
 */
class ImplantableDevice extends AbstractModel
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

    public function getDeviceType(): ?string
    {
        return $this->data['device_type'] ?? null;
    }

    public function setDeviceType(string $type): self
    {
        $this->data['device_type'] = $type;
        return $this;
    }

    public function getDeviceIdentifier(): ?string
    {
        return $this->data['device_identifier'] ?? null;
    }

    public function setDeviceIdentifier(string $identifier): self
    {
        $this->data['device_identifier'] = $identifier;
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

    public function getModelNumber(): ?string
    {
        return $this->data['model_number'] ?? null;
    }

    public function setModelNumber(string $modelNumber): self
    {
        $this->data['model_number'] = $modelNumber;
        return $this;
    }

    public function getSerialNumber(): ?string
    {
        return $this->data['serial_number'] ?? null;
    }

    public function setSerialNumber(string $serialNumber): self
    {
        $this->data['serial_number'] = $serialNumber;
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

    public function getImplantDate(): ?string
    {
        return $this->data['implant_date'] ?? null;
    }

    public function setImplantDate(string $date): self
    {
        $this->data['implant_date'] = $date;
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

    public function getAnatomicLocation(): ?string
    {
        return $this->data['anatomic_location'] ?? null;
    }

    public function setAnatomicLocation(string $location): self
    {
        $this->data['anatomic_location'] = $location;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->data['status'] ?? null;
    }

    public function setStatus(string $status): self
    {
        $this->data['status'] = $status;
        return $this;
    }

    public function getRemovalDate(): ?string
    {
        return $this->data['removal_date'] ?? null;
    }

    public function setRemovalDate(string $date): self
    {
        $this->data['removal_date'] = $date;
        return $this;
    }

    public function getExpirationDate(): ?string
    {
        return $this->data['expiration_date'] ?? null;
    }

    public function setExpirationDate(string $date): self
    {
        $this->data['expiration_date'] = $date;
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

    public function isActive(): bool
    {
        return ($this->data['status'] ?? '') === 'active';
    }

    public function isRemoved(): bool
    {
        return ($this->data['status'] ?? '') === 'removed';
    }

    public function getCreatedAt(): ?string
    {
        return $this->data['created_at'] ?? null;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->data['updated_at'] ?? null;
    }
}

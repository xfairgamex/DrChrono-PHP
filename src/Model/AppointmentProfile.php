<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Appointment Profile Model
 *
 * Represents an appointment type with default duration and settings
 */
class AppointmentProfile extends AbstractModel
{
    protected ?int $id = null;
    protected ?string $name = null;
    protected ?int $duration = null;
    protected ?int $doctor = null;
    protected ?string $color = null;
    protected ?bool $archived = null;
    protected ?bool $onlineSchedulingEnabled = null;
    protected ?int $sortOrder = null;
    protected ?string $updatedAt = null;
    protected ?string $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function isArchived(): bool
    {
        return $this->archived ?? false;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;
        return $this;
    }

    public function isOnlineBookable(): bool
    {
        return $this->onlineSchedulingEnabled ?? false;
    }

    public function setOnlineSchedulingEnabled(bool $enabled): self
    {
        $this->onlineSchedulingEnabled = $enabled;
        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }
}

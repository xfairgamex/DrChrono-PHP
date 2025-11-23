<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Patient Flag Type Model
 *
 * Represents a custom patient flag type definition
 */
class PatientFlagType extends AbstractModel
{
    protected ?int $id = null;
    protected ?string $name = null;
    protected ?string $color = null;
    protected ?int $priority = null;
    protected ?int $doctor = null;
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

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;
        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getDoctorId(): ?int
    {
        return $this->doctor;
    }

    public function setDoctor(?int $doctorId): self
    {
        $this->doctor = $doctorId;
        return $this;
    }
}

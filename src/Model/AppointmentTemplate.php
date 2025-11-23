<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Appointment Template Model
 *
 * Represents a recurring appointment availability block
 */
class AppointmentTemplate extends AbstractModel
{
    protected ?int $id = null;
    protected ?int $doctor = null;
    protected ?int $office = null;
    protected ?int $profile = null;
    protected ?int $dayOfWeek = null;
    protected ?string $startTime = null;
    protected ?int $duration = null;
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

    public function getOfficeId(): ?int
    {
        return $this->office;
    }

    public function setOffice(int $officeId): self
    {
        $this->office = $officeId;
        return $this;
    }

    public function getProfileId(): ?int
    {
        return $this->profile;
    }

    public function setProfile(int $profileId): self
    {
        $this->profile = $profileId;
        return $this;
    }

    public function getDayOfWeek(): ?int
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(int $dayOfWeek): self
    {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    public function getStartTime(): ?string
    {
        return $this->startTime;
    }

    public function setStartTime(string $startTime): self
    {
        $this->startTime = $startTime;
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
}

<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Patient model
 */
class Patient extends AbstractModel
{
    protected ?int $id = null;
    protected ?string $firstName = null;
    protected ?string $lastName = null;
    protected ?string $middleName = null;
    protected ?string $email = null;
    protected ?string $cellPhone = null;
    protected ?string $homePhone = null;
    protected ?string $officePhone = null;
    protected ?string $dateOfBirth = null;
    protected ?string $gender = null;
    protected ?string $socialSecurityNumber = null;
    protected ?string $address = null;
    protected ?string $city = null;
    protected ?string $state = null;
    protected ?string $zipCode = null;
    protected ?string $ethnicity = null;
    protected ?string $race = null;
    protected ?string $preferredLanguage = null;
    protected ?int $doctor = null;
    protected ?string $chartId = null;
    protected ?string $patientStatus = null;
    protected ?string $patientPhoto = null;
    protected ?string $updatedAt = null;
    protected ?string $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getDateOfBirth(): ?string
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(string $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    public function getFullName(): string
    {
        return trim("{$this->firstName} {$this->middleName} {$this->lastName}");
    }

    public function getDoctor(): ?int
    {
        return $this->doctor;
    }

    public function setDoctor(int $doctor): self
    {
        $this->doctor = $doctor;
        return $this;
    }

    public function getChartId(): ?string
    {
        return $this->chartId;
    }
}

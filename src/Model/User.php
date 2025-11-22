<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * User/Doctor model
 */
class User extends AbstractModel
{
    protected ?int $id = null;
    protected ?string $username = null;
    protected ?string $firstName = null;
    protected ?string $lastName = null;
    protected ?string $email = null;
    protected ?string $specialty = null;
    protected ?string $npi = null;
    protected ?string $jobTitle = null;
    protected ?bool $isStaff = null;
    protected ?string $timezone = null;
    protected ?string $profilePicture = null;
    protected ?string $updatedAt = null;
    protected ?string $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFullName(): string
    {
        return trim("{$this->firstName} {$this->lastName}");
    }

    public function getSpecialty(): ?string
    {
        return $this->specialty;
    }

    public function getNpi(): ?string
    {
        return $this->npi;
    }

    public function isStaff(): bool
    {
        return $this->isStaff ?? false;
    }

    public function isDoctor(): bool
    {
        return !$this->isStaff();
    }
}

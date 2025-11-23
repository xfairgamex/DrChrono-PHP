<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Doctor Model
 *
 * Represents a doctor/provider in the practice group.
 */
class Doctor extends AbstractModel
{
    /**
     * Get doctor ID
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get first name
     */
    public function getFirstName(): ?string
    {
        return $this->data['first_name'] ?? null;
    }

    /**
     * Set first name
     */
    public function setFirstName(string $firstName): self
    {
        $this->data['first_name'] = $firstName;
        return $this;
    }

    /**
     * Get last name
     */
    public function getLastName(): ?string
    {
        return $this->data['last_name'] ?? null;
    }

    /**
     * Set last name
     */
    public function setLastName(string $lastName): self
    {
        $this->data['last_name'] = $lastName;
        return $this;
    }

    /**
     * Get full name
     */
    public function getFullName(): string
    {
        $name = trim(($this->getFirstName() ?? '') . ' ' . ($this->getLastName() ?? ''));

        if ($suffix = $this->getSuffix()) {
            $name .= ', ' . $suffix;
        }

        return $name;
    }

    /**
     * Get specialty
     */
    public function getSpecialty(): ?string
    {
        return $this->data['specialty'] ?? null;
    }

    /**
     * Set specialty
     */
    public function setSpecialty(string $specialty): self
    {
        $this->data['specialty'] = $specialty;
        return $this;
    }

    /**
     * Get NPI number
     */
    public function getNpiNumber(): ?string
    {
        return $this->data['npi_number'] ?? null;
    }

    /**
     * Set NPI number
     */
    public function setNpiNumber(string $npiNumber): self
    {
        $this->data['npi_number'] = $npiNumber;
        return $this;
    }

    /**
     * Get email address
     */
    public function getEmail(): ?string
    {
        return $this->data['email'] ?? null;
    }

    /**
     * Set email address
     */
    public function setEmail(string $email): self
    {
        $this->data['email'] = $email;
        return $this;
    }

    /**
     * Get office phone
     */
    public function getOfficePhone(): ?string
    {
        return $this->data['office_phone'] ?? null;
    }

    /**
     * Set office phone
     */
    public function setOfficePhone(string $officePhone): self
    {
        $this->data['office_phone'] = $officePhone;
        return $this;
    }

    /**
     * Get cell phone
     */
    public function getCellPhone(): ?string
    {
        return $this->data['cell_phone'] ?? null;
    }

    /**
     * Set cell phone
     */
    public function setCellPhone(string $cellPhone): self
    {
        $this->data['cell_phone'] = $cellPhone;
        return $this;
    }

    /**
     * Get website
     */
    public function getWebsite(): ?string
    {
        return $this->data['website'] ?? null;
    }

    /**
     * Set website
     */
    public function setWebsite(string $website): self
    {
        $this->data['website'] = $website;
        return $this;
    }

    /**
     * Get country
     */
    public function getCountry(): ?string
    {
        return $this->data['country'] ?? null;
    }

    /**
     * Set country
     */
    public function setCountry(string $country): self
    {
        $this->data['country'] = $country;
        return $this;
    }

    /**
     * Get timezone
     */
    public function getTimezone(): ?string
    {
        return $this->data['timezone'] ?? null;
    }

    /**
     * Set timezone
     */
    public function setTimezone(string $timezone): self
    {
        $this->data['timezone'] = $timezone;
        return $this;
    }

    /**
     * Get practice group ID
     */
    public function getPracticeGroup(): ?int
    {
        return $this->data['practice_group'] ?? null;
    }

    /**
     * Set practice group ID
     */
    public function setPracticeGroup(int $practiceGroup): self
    {
        $this->data['practice_group'] = $practiceGroup;
        return $this;
    }

    /**
     * Get practice group name
     */
    public function getPracticeGroupName(): ?string
    {
        return $this->data['practice_group_name'] ?? null;
    }

    /**
     * Set practice group name
     */
    public function setPracticeGroupName(string $practiceGroupName): self
    {
        $this->data['practice_group_name'] = $practiceGroupName;
        return $this;
    }

    /**
     * Get profile picture URL
     */
    public function getProfilePicture(): ?string
    {
        return $this->data['profile_picture'] ?? null;
    }

    /**
     * Set profile picture URL
     */
    public function setProfilePicture(string $profilePicture): self
    {
        $this->data['profile_picture'] = $profilePicture;
        return $this;
    }

    /**
     * Get suffix (e.g., MD, DO, NP)
     */
    public function getSuffix(): ?string
    {
        return $this->data['suffix'] ?? null;
    }

    /**
     * Set suffix
     */
    public function setSuffix(string $suffix): self
    {
        $this->data['suffix'] = $suffix;
        return $this;
    }

    /**
     * Check if account is suspended
     */
    public function isSuspended(): bool
    {
        return $this->data['is_account_suspended'] ?? false;
    }

    /**
     * Check if account is active (not suspended)
     */
    public function isActive(): bool
    {
        return !$this->isSuspended();
    }

    /**
     * Set account suspension status
     */
    public function setAccountSuspended(bool $suspended): self
    {
        $this->data['is_account_suspended'] = $suspended;
        return $this;
    }
}

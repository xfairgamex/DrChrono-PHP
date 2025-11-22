<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Office model
 */
class Office extends AbstractModel
{
    protected ?int $id = null;
    protected ?string $name = null;
    protected ?string $address = null;
    protected ?string $city = null;
    protected ?string $state = null;
    protected ?string $zipCode = null;
    protected ?string $country = null;
    protected ?string $phoneNumber = null;
    protected ?string $faxNumber = null;
    protected ?string $startTime = null;
    protected ?string $endTime = null;
    protected ?bool $onlineScheduling = null;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getFullAddress(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zipCode,
        ]);
        return implode(', ', $parts);
    }
}

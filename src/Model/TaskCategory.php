<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Task Category Model
 *
 * Represents a task category for organizational structure
 */
class TaskCategory extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    public function setName(string $name): self
    {
        $this->data['name'] = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->data['description'] ?? null;
    }

    public function setDescription(string $description): self
    {
        $this->data['description'] = $description;
        return $this;
    }

    public function getColor(): ?string
    {
        return $this->data['color'] ?? null;
    }

    public function setColor(string $color): self
    {
        $this->data['color'] = $color;
        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->data['sort_order'] ?? null;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->data['sort_order'] = $sortOrder;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->data['is_active'] ?? true;
    }

    public function setActive(bool $isActive): self
    {
        $this->data['is_active'] = $isActive;
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
}

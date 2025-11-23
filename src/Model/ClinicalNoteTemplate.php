<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Clinical Note Template Model
 *
 * Represents a template for clinical documentation
 */
class ClinicalNoteTemplate extends AbstractModel
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

    public function getDoctorId(): ?int
    {
        return $this->data['doctor'] ?? null;
    }

    public function setDoctor(int $doctorId): self
    {
        $this->data['doctor'] = $doctorId;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->data['content'] ?? null;
    }

    public function setContent(string $content): self
    {
        $this->data['content'] = $content;
        return $this;
    }

    public function getSections(): ?array
    {
        return $this->data['sections'] ?? null;
    }

    public function setSections(array $sections): self
    {
        $this->data['sections'] = $sections;
        return $this;
    }

    public function isDefault(): bool
    {
        return !empty($this->data['is_default']);
    }

    public function setDefault(bool $isDefault): self
    {
        $this->data['is_default'] = $isDefault;
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

<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Task Template Model
 *
 * Represents a reusable task template for standardized workflows
 */
class TaskTemplate extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    public function getTitle(): ?string
    {
        return $this->data['title'] ?? null;
    }

    public function setTitle(string $title): self
    {
        $this->data['title'] = $title;
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

    public function getDoctorId(): ?int
    {
        return $this->data['doctor'] ?? null;
    }

    public function setDoctor(int $doctorId): self
    {
        $this->data['doctor'] = $doctorId;
        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->data['category'] ?? null;
    }

    public function setCategory(int $categoryId): self
    {
        $this->data['category'] = $categoryId;
        return $this;
    }

    public function getStatusId(): ?int
    {
        return $this->data['status'] ?? null;
    }

    public function setStatus(int $statusId): self
    {
        $this->data['status'] = $statusId;
        return $this;
    }

    public function getAssignedTo(): ?int
    {
        return $this->data['assigned_to'] ?? null;
    }

    public function setAssignedTo(int $userId): self
    {
        $this->data['assigned_to'] = $userId;
        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->data['priority'] ?? null;
    }

    public function setPriority(string $priority): self
    {
        $this->data['priority'] = $priority;
        return $this;
    }

    public function getDueDays(): ?int
    {
        return $this->data['due_days'] ?? null;
    }

    public function setDueDays(int $dueDays): self
    {
        $this->data['due_days'] = $dueDays;
        return $this;
    }

    public function getChecklistItems(): ?array
    {
        return $this->data['checklist_items'] ?? null;
    }

    public function setChecklistItems(array $checklistItems): self
    {
        $this->data['checklist_items'] = $checklistItems;
        return $this;
    }

    public function getTags(): ?array
    {
        return $this->data['tags'] ?? null;
    }

    public function setTags(array $tags): self
    {
        $this->data['tags'] = $tags;
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

    /**
     * Check if template is high priority
     */
    public function isHighPriority(): bool
    {
        $priority = $this->getPriority();
        return in_array($priority, ['high', 'urgent']);
    }

    /**
     * Check if template is urgent
     */
    public function isUrgent(): bool
    {
        return $this->getPriority() === 'urgent';
    }
}

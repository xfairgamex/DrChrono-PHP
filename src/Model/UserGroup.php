<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * User Group Model
 *
 * Represents a user group for role-based access control.
 */
class UserGroup extends AbstractModel
{
    /**
     * Get group ID
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get group name
     */
    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    /**
     * Set group name
     */
    public function setName(string $name): self
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * Get group description
     */
    public function getDescription(): ?string
    {
        return $this->data['description'] ?? null;
    }

    /**
     * Set group description
     */
    public function setDescription(string $description): self
    {
        $this->data['description'] = $description;
        return $this;
    }

    /**
     * Get permissions
     */
    public function getPermissions(): ?array
    {
        return $this->data['permissions'] ?? null;
    }

    /**
     * Set permissions
     */
    public function setPermissions(array $permissions): self
    {
        $this->data['permissions'] = $permissions;
        return $this;
    }

    /**
     * Add a permission
     */
    public function addPermission(string $permission): self
    {
        $permissions = $this->getPermissions() ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->setPermissions($permissions);
        }
        return $this;
    }

    /**
     * Remove a permission
     */
    public function removePermission(string $permission): self
    {
        $permissions = $this->getPermissions() ?? [];
        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        $this->setPermissions(array_values($permissions));
        return $this;
    }

    /**
     * Check if group has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->getPermissions() ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Get users in this group
     */
    public function getUsers(): ?array
    {
        return $this->data['users'] ?? null;
    }

    /**
     * Set users in this group
     */
    public function setUsers(array $users): self
    {
        $this->data['users'] = $users;
        return $this;
    }

    /**
     * Get created timestamp
     */
    public function getCreatedAt(): ?string
    {
        return $this->data['created_at'] ?? null;
    }

    /**
     * Get updated timestamp
     */
    public function getUpdatedAt(): ?string
    {
        return $this->data['updated_at'] ?? null;
    }

    /**
     * Check if group is active
     */
    public function isActive(): bool
    {
        return $this->data['is_active'] ?? true;
    }

    /**
     * Set active status
     */
    public function setActive(bool $isActive): self
    {
        $this->data['is_active'] = $isActive;
        return $this;
    }
}

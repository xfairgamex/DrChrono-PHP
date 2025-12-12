<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * User Groups Resource - User permission groups and roles
 *
 * Manage user groups for organizing permissions and access control.
 * User groups allow you to define roles and assign users to them for
 * streamlined permission management across your practice.
 *
 * API Endpoint: /api/user_groups
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#user_groups
 *
 * Use Cases:
 * - Role-based access control (RBAC)
 * - Permission management
 * - User organization by department or function
 * - Compliance and audit requirements
 */
class UserGroupsResource extends AbstractResource
{
    protected string $resourcePath = '/api/user_groups';

    /**
     * List all user groups
     *
     * @param array $filters Optional filters:
     *   - 'name' (string): Filter by group name
     * @return PagedCollection Collection of user groups
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific user group by ID
     *
     * @param int|string $groupId User group ID
     * @return array User group information
     */
    public function get(int|string $groupId): array
    {
        return parent::get($groupId);
    }

    /**
     * Create a new user group
     *
     * Typical fields:
     * - name (string): Group name (required)
     * - description (string): Group description
     * - permissions (array): Array of permission identifiers
     *
     * @param array $data User group data
     * @return array Created user group
     */
    public function createGroup(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update a user group
     *
     * @param int $groupId User group ID
     * @param array $data Updated data
     * @return array Updated user group
     */
    public function updateGroup(int $groupId, array $data): array
    {
        return $this->update($groupId, $data);
    }

    /**
     * Delete a user group
     *
     * @param int $groupId User group ID
     * @return void
     */
    public function deleteGroup(int $groupId): void
    {
        $this->delete($groupId);
    }

    /**
     * Get user group by name
     *
     * Searches for a user group by exact name match.
     *
     * @param string $name Group name
     * @return array|null User group or null if not found
     */
    public function getByName(string $name): ?array
    {
        $collection = $this->list(['name' => $name]);
        $items = $collection->getItems();

        return !empty($items) ? $items[0] : null;
    }

    /**
     * Search user groups by name (partial match)
     *
     * @param string $searchTerm Search term
     * @return array List of matching user groups
     */
    public function search(string $searchTerm): array
    {
        $searchTerm = strtolower($searchTerm);
        $groups = [];
        $collection = $this->list();

        foreach ($collection as $group) {
            $name = strtolower($group['name'] ?? '');

            if (str_contains($name, $searchTerm)) {
                $groups[] = $group;
            }
        }

        return $groups;
    }

    /**
     * Get users in a specific group
     *
     * Note: This may require the users endpoint and filtering.
     * The actual implementation depends on the API structure.
     *
     * @param int $groupId User group ID
     * @return array Group details with users if available
     */
    public function getGroupUsers(int $groupId): array
    {
        return $this->get($groupId);
    }

    /**
     * Clone/duplicate a user group
     *
     * Creates a copy of an existing user group with a new name.
     *
     * @param int $groupId Source group ID
     * @param string $newName Name for the new group
     * @return array Created user group
     */
    public function duplicateGroup(int $groupId, string $newName): array
    {
        $sourceGroup = $this->get($groupId);

        // Remove read-only fields
        unset($sourceGroup['id'], $sourceGroup['created_at'], $sourceGroup['updated_at']);

        // Set new name
        $sourceGroup['name'] = $newName;

        return $this->createGroup($sourceGroup);
    }
}

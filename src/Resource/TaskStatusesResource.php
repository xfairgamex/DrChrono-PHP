<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Task Statuses - Define and manage custom task status values
 *
 * Task statuses enable tracking of task progression through various
 * workflow states with customizable status definitions.
 *
 * API Endpoint: /api/task_statuses
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#task_statuses
 */
class TaskStatusesResource extends AbstractResource
{
    protected string $resourcePath = '/api/task_statuses';

    /**
     * List task statuses
     *
     * @param array $filters Optional filters:
     *   - 'since' (string): Filter by update date
     *   - 'name' (string): Filter by status name
     *   - 'is_active' (bool): Filter by active status
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific task status
     *
     * @param int|string $statusId Status ID
     * @return array Status data
     */
    public function get(int|string $statusId): array
    {
        return parent::get($statusId);
    }

    /**
     * Create a new task status
     *
     * Required fields:
     * - name (string): Status name
     *
     * Optional fields:
     * - description (string): Status description
     * - color (string): Color code for visual identification
     * - sort_order (int): Display order
     * - is_active (bool): Whether status is active
     * - is_completed (bool): Whether this status indicates completion
     * - is_default (bool): Whether this is the default status
     *
     * @param array $data Status data
     * @return array Created status
     */
    public function createStatus(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing task status
     *
     * @param int $statusId Status ID
     * @param array $data Updated data
     * @return array Updated status
     */
    public function updateStatus(int $statusId, array $data): array
    {
        return $this->update($statusId, $data);
    }

    /**
     * Delete a task status
     *
     * Note: May fail if tasks are currently using this status
     *
     * @param int $statusId Status ID
     * @return void
     */
    public function deleteStatus(int $statusId): void
    {
        $this->delete($statusId);
    }

    /**
     * Get task status by name
     *
     * @param string $name Status name
     * @return array|null Status data or null if not found
     */
    public function getByName(string $name): ?array
    {
        $statuses = $this->list(['name' => $name]);
        $items = $statuses->getItems();
        return !empty($items) ? $items[0] : null;
    }

    /**
     * Get all active statuses
     *
     * @return PagedCollection
     */
    public function listActive(): PagedCollection
    {
        return $this->list(['is_active' => 'true']);
    }

    /**
     * Get statuses sorted by order
     *
     * @return PagedCollection
     */
    public function listOrdered(): PagedCollection
    {
        return $this->list(['ordering' => 'sort_order']);
    }

    /**
     * Get the default status
     *
     * @return array|null Default status or null if not found
     */
    public function getDefault(): ?array
    {
        $statuses = $this->list(['is_default' => 'true']);
        $items = $statuses->getItems();
        return !empty($items) ? $items[0] : null;
    }

    /**
     * Get all completion statuses
     *
     * @return PagedCollection
     */
    public function listCompletionStatuses(): PagedCollection
    {
        return $this->list(['is_completed' => 'true']);
    }

    /**
     * Archive a status (set inactive)
     *
     * @param int $statusId Status ID
     * @return array Updated status
     */
    public function archive(int $statusId): array
    {
        return $this->updateStatus($statusId, ['is_active' => false]);
    }

    /**
     * Restore an archived status (set active)
     *
     * @param int $statusId Status ID
     * @return array Updated status
     */
    public function restore(int $statusId): array
    {
        return $this->updateStatus($statusId, ['is_active' => true]);
    }

    /**
     * Set a status as default
     *
     * @param int $statusId Status ID
     * @return array Updated status
     */
    public function setAsDefault(int $statusId): array
    {
        return $this->updateStatus($statusId, ['is_default' => true]);
    }
}

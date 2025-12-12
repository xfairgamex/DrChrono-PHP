<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Task Categories - Organize tasks through categorization
 *
 * Task categories provide organizational structure for tasks,
 * enabling improved workflow management and filtering capabilities.
 *
 * API Endpoint: /api/task_categories
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#task_categories
 */
class TaskCategoriesResource extends AbstractResource
{
    protected string $resourcePath = '/api/task_categories';

    /**
     * List task categories
     *
     * @param array $filters Optional filters:
     *   - 'since' (string): Filter by update date
     *   - 'name' (string): Filter by category name
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific task category
     *
     * @param int|string $categoryId Category ID
     * @return array Category data
     */
    public function get(int|string $categoryId): array
    {
        return parent::get($categoryId);
    }

    /**
     * Create a new task category
     *
     * Required fields:
     * - name (string): Category name
     *
     * Optional fields:
     * - description (string): Category description
     * - color (string): Color code for visual identification
     * - sort_order (int): Display order
     * - is_active (bool): Whether category is active
     *
     * @param array $data Category data
     * @return array Created category
     */
    public function createCategory(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing task category
     *
     * @param int $categoryId Category ID
     * @param array $data Updated data
     * @return array Updated category
     */
    public function updateCategory(int $categoryId, array $data): array
    {
        return $this->update($categoryId, $data);
    }

    /**
     * Delete a task category
     *
     * @param int $categoryId Category ID
     * @return void
     */
    public function deleteCategory(int $categoryId): void
    {
        $this->delete($categoryId);
    }

    /**
     * Get task category by name
     *
     * @param string $name Category name
     * @return array|null Category data or null if not found
     */
    public function getByName(string $name): ?array
    {
        $categories = $this->list(['name' => $name]);
        $items = $categories->getItems();
        return !empty($items) ? $items[0] : null;
    }

    /**
     * Get all active categories
     *
     * @return PagedCollection
     */
    public function listActive(): PagedCollection
    {
        return $this->list(['is_active' => 'true']);
    }

    /**
     * Get categories sorted by order
     *
     * @return PagedCollection
     */
    public function listOrdered(): PagedCollection
    {
        return $this->list(['ordering' => 'sort_order']);
    }

    /**
     * Archive a category (set inactive)
     *
     * @param int $categoryId Category ID
     * @return array Updated category
     */
    public function archive(int $categoryId): array
    {
        return $this->updateCategory($categoryId, ['is_active' => false]);
    }

    /**
     * Restore an archived category (set active)
     *
     * @param int $categoryId Category ID
     * @return array Updated category
     */
    public function restore(int $categoryId): array
    {
        return $this->updateCategory($categoryId, ['is_active' => true]);
    }
}

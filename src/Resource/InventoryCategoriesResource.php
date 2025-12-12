<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Inventory Categories - Manage inventory categorization for vaccines and supplies
 *
 * Inventory categories organize vaccine and medical supply tracking within
 * the practice system, enabling better inventory management and reporting.
 *
 * API Endpoint: /api/inventory_categories
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#inventory_categories
 */
class InventoryCategoriesResource extends AbstractResource
{
    protected string $resourcePath = '/api/inventory_categories';

    /**
     * List inventory categories
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
     * Get a specific inventory category
     *
     * @param int|string $categoryId Category ID
     * @return array Category data
     */
    public function get(int|string $categoryId): array
    {
        return parent::get($categoryId);
    }

    /**
     * Create a new inventory category
     *
     * Required fields:
     * - name (string): Category name
     *
     * Optional fields:
     * - description (string): Category description
     * - sort_order (int): Display order
     *
     * @param array $data Category data
     * @return array Created category
     */
    public function createCategory(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing inventory category
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
     * Delete an inventory category
     *
     * @param int $categoryId Category ID
     * @return void
     */
    public function deleteCategory(int $categoryId): void
    {
        $this->delete($categoryId);
    }

    /**
     * Get inventory category by name
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
     * Get all categories sorted by order
     *
     * @return PagedCollection
     */
    public function listOrdered(): PagedCollection
    {
        return $this->list(['ordering' => 'sort_order']);
    }
}

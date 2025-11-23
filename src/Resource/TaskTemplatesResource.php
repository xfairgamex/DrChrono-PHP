<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Task Templates - Manage reusable task templates for standardized workflows
 *
 * Task templates enable creation of standardized, reusable task definitions
 * that can be applied to recurring workflow scenarios.
 *
 * API Endpoint: /api/task_templates
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#task_templates
 */
class TaskTemplatesResource extends AbstractResource
{
    protected string $resourcePath = '/api/task_templates';

    /**
     * List task templates
     *
     * @param array $filters Optional filters:
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'category' (int): Filter by category ID
     *   - 'since' (string): Filter by update date
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific task template
     *
     * @param int|string $templateId Template ID
     * @return array Template data
     */
    public function get(int|string $templateId): array
    {
        return parent::get($templateId);
    }

    /**
     * List templates by doctor
     *
     * @param int $doctorId Doctor ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * List templates by category
     *
     * @param int $categoryId Category ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByCategory(int $categoryId, array $filters = []): PagedCollection
    {
        $filters['category'] = $categoryId;
        return $this->list($filters);
    }

    /**
     * Create a new task template
     *
     * Required fields:
     * - title (string): Template title
     *
     * Optional fields:
     * - description (string): Template description
     * - doctor (int): Doctor ID
     * - category (int): Category ID
     * - status (int): Default status ID
     * - assigned_to (int): Default assignee user ID
     * - priority (string): Default priority (low, medium, high, urgent)
     * - due_days (int): Days until due (relative to creation)
     * - checklist_items (array): Default checklist items
     * - tags (array): Template tags
     *
     * @param array $data Template data
     * @return array Created template
     */
    public function createTemplate(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing task template
     *
     * @param int $templateId Template ID
     * @param array $data Updated data
     * @return array Updated template
     */
    public function updateTemplate(int $templateId, array $data): array
    {
        return $this->update($templateId, $data);
    }

    /**
     * Delete a task template
     *
     * @param int $templateId Template ID
     * @return void
     */
    public function deleteTemplate(int $templateId): void
    {
        $this->delete($templateId);
    }

    /**
     * Create a task from a template
     *
     * @param int $templateId Template ID
     * @param array $overrides Optional field overrides
     * @return array Created task
     */
    public function instantiateTemplate(int $templateId, array $overrides = []): array
    {
        return $this->httpClient->post(
            "{$this->resourcePath}/{$templateId}/instantiate",
            $overrides
        );
    }

    /**
     * Duplicate a task template
     *
     * @param int $templateId Template ID
     * @param string $newTitle New template title
     * @return array Duplicated template
     */
    public function duplicateTemplate(int $templateId, string $newTitle): array
    {
        $template = $this->get($templateId);
        unset($template['id']);
        $template['title'] = $newTitle;
        return $this->createTemplate($template);
    }

    /**
     * Get templates by priority
     *
     * @param string $priority Priority level (low, medium, high, urgent)
     * @return PagedCollection
     */
    public function getByPriority(string $priority): PagedCollection
    {
        return $this->list(['priority' => $priority]);
    }
}

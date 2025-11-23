<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Clinical Note Templates - Manage templates for clinical documentation
 *
 * Templates define the structure and default content for clinical notes,
 * enabling standardized documentation across the practice.
 *
 * API Endpoint: /api/clinical_note_templates
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#clinical_note_templates
 */
class ClinicalNoteTemplatesResource extends AbstractResource
{
    protected string $resourcePath = '/api/clinical_note_templates';

    /**
     * List clinical note templates
     *
     * @param array $filters Optional filters:
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Filter by update date
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific clinical note template
     *
     * @param int|string $templateId Template ID
     * @return array Template data
     */
    public function get(int|string $templateId): array
    {
        return parent::get($templateId);
    }

    /**
     * List templates for a specific doctor
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
     * Create a new clinical note template
     *
     * Required fields:
     * - name (string): Template name
     * - doctor (int): Doctor ID
     *
     * Optional fields:
     * - content (string): Template content
     * - sections (array): Template sections
     * - is_default (bool): Whether this is a default template
     *
     * @param array $data Template data
     * @return array Created template
     */
    public function createTemplate(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing template
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
     * Delete a template
     *
     * @param int $templateId Template ID
     * @return void
     */
    public function deleteTemplate(int $templateId): void
    {
        $this->delete($templateId);
    }

    /**
     * Get default templates for a doctor
     *
     * Returns templates marked as default for quick access
     *
     * @param int $doctorId Doctor ID
     * @return array Default templates
     */
    public function getDefaultTemplates(int $doctorId): array
    {
        $templates = $this->listByDoctor($doctorId);
        $defaults = [];

        foreach ($templates as $template) {
            if (!empty($template['is_default'])) {
                $defaults[] = $template;
            }
        }

        return $defaults;
    }

    /**
     * Clone an existing template
     *
     * Useful for creating variations of existing templates
     *
     * @param int $templateId Source template ID
     * @param string $newName Name for the cloned template
     * @param int|null $doctorId Doctor ID (defaults to source template's doctor)
     * @return array Newly created template
     */
    public function cloneTemplate(int $templateId, string $newName, ?int $doctorId = null): array
    {
        $source = $this->get($templateId);

        $data = [
            'name' => $newName,
            'doctor' => $doctorId ?? $source['doctor'],
            'content' => $source['content'] ?? '',
            'is_default' => false,
        ];

        if (isset($source['sections'])) {
            $data['sections'] = $source['sections'];
        }

        return $this->createTemplate($data);
    }
}

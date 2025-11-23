<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Clinical Note Field Values - Store values for custom clinical note fields
 *
 * Field values store the actual data entered for custom fields defined
 * in clinical note field types.
 *
 * API Endpoint: /api/clinical_note_field_values
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#clinical_note_field_values
 */
class ClinicalNoteFieldValuesResource extends AbstractResource
{
    protected string $resourcePath = '/api/clinical_note_field_values';

    /**
     * List clinical note field values
     *
     * @param array $filters Optional filters:
     *   - 'clinical_note' (int): Filter by clinical note ID
     *   - 'field_type' (int): Filter by field type ID
     *   - 'since' (string): Filter by update date
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific field value
     *
     * @param int|string $fieldValueId Field value ID
     * @return array Field value data
     */
    public function get(int|string $fieldValueId): array
    {
        return parent::get($fieldValueId);
    }

    /**
     * List field values for a specific clinical note
     *
     * @param int $clinicalNoteId Clinical note ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByClinicalNote(int $clinicalNoteId, array $filters = []): PagedCollection
    {
        $filters['clinical_note'] = $clinicalNoteId;
        return $this->list($filters);
    }

    /**
     * List field values by field type
     *
     * @param int $fieldTypeId Field type ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByFieldType(int $fieldTypeId, array $filters = []): PagedCollection
    {
        $filters['field_type'] = $fieldTypeId;
        return $this->list($filters);
    }

    /**
     * Create a new field value
     *
     * Required fields:
     * - clinical_note (int): Clinical note ID
     * - field_type (int): Field type ID
     * - value (mixed): Field value
     *
     * @param array $data Field value data
     * @return array Created field value
     */
    public function createFieldValue(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing field value
     *
     * @param int $fieldValueId Field value ID
     * @param array $data Updated data
     * @return array Updated field value
     */
    public function updateFieldValue(int $fieldValueId, array $data): array
    {
        return $this->update($fieldValueId, $data);
    }

    /**
     * Delete a field value
     *
     * @param int $fieldValueId Field value ID
     * @return void
     */
    public function deleteFieldValue(int $fieldValueId): void
    {
        $this->delete($fieldValueId);
    }

    /**
     * Update or create field value for a clinical note
     *
     * Convenience method that updates if exists, creates if not
     *
     * @param int $clinicalNoteId Clinical note ID
     * @param int $fieldTypeId Field type ID
     * @param mixed $value Field value
     * @return array Field value data
     */
    public function upsertValue(int $clinicalNoteId, int $fieldTypeId, $value): array
    {
        $existing = $this->listByClinicalNote($clinicalNoteId);

        foreach ($existing as $fieldValue) {
            if (($fieldValue['field_type'] ?? null) === $fieldTypeId) {
                return $this->updateFieldValue($fieldValue['id'], ['value' => $value]);
            }
        }

        return $this->createFieldValue([
            'clinical_note' => $clinicalNoteId,
            'field_type' => $fieldTypeId,
            'value' => $value,
        ]);
    }
}

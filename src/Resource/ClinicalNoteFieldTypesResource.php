<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Clinical Note Field Types - Define custom field types for clinical notes
 *
 * Field types allow practices to define custom data points to capture
 * in clinical notes beyond the standard fields.
 *
 * API Endpoint: /api/clinical_note_field_types
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#clinical_note_field_types
 */
class ClinicalNoteFieldTypesResource extends AbstractResource
{
    protected string $resourcePath = '/api/clinical_note_field_types';

    /**
     * List clinical note field types
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
     * Get a specific field type
     *
     * @param int|string $fieldTypeId Field type ID
     * @return array Field type data
     */
    public function get(int|string $fieldTypeId): array
    {
        return parent::get($fieldTypeId);
    }

    /**
     * List field types for a specific doctor
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
     * Create a new field type
     *
     * Required fields:
     * - name (string): Field type name
     * - data_type (string): Type of data (text, number, date, select, etc.)
     *
     * Optional fields:
     * - doctor (int): Doctor ID
     * - options (array): Options for select-type fields
     * - is_required (bool): Whether field is required
     * - default_value (mixed): Default value
     *
     * @param array $data Field type data
     * @return array Created field type
     */
    public function createFieldType(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing field type
     *
     * @param int $fieldTypeId Field type ID
     * @param array $data Updated data
     * @return array Updated field type
     */
    public function updateFieldType(int $fieldTypeId, array $data): array
    {
        return $this->update($fieldTypeId, $data);
    }

    /**
     * Delete a field type
     *
     * @param int $fieldTypeId Field type ID
     * @return void
     */
    public function deleteFieldType(int $fieldTypeId): void
    {
        $this->delete($fieldTypeId);
    }

    /**
     * Get field types by data type
     *
     * Filter field types by their data type (e.g., text, number, date)
     *
     * @param string $dataType Data type to filter by
     * @param array $filters Additional filters
     * @return array Matching field types
     */
    public function getByDataType(string $dataType, array $filters = []): array
    {
        $fieldTypes = $this->list($filters);
        $matching = [];

        foreach ($fieldTypes as $fieldType) {
            if (($fieldType['data_type'] ?? '') === $dataType) {
                $matching[] = $fieldType;
            }
        }

        return $matching;
    }
}

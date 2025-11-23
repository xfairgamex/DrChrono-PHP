<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Custom Appointment Fields - Manage custom metadata for appointments
 *
 * Custom appointment fields allow practices to capture additional structured
 * data for appointments beyond the standard fields.
 *
 * API Endpoint: /api/custom_appointment_fields
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#custom_appointment_fields
 */
class CustomAppointmentFieldsResource extends AbstractResource
{
    protected string $resourcePath = '/api/custom_appointment_fields';

    /**
     * List custom appointment fields
     *
     * @param array $filters Optional filters:
     *   - 'doctor' (int): Filter by doctor ID
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get custom fields for a specific doctor
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
     * Create custom appointment field
     *
     * Required fields:
     * - name (string): Field name
     * - field_type (string): Field type (e.g., 'text', 'dropdown', 'checkbox')
     *
     * @param array $data Field data
     * @return array Created field
     */
    public function createField(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update custom appointment field
     *
     * @param int $fieldId Field ID
     * @param array $data Updated data
     * @return array Updated field
     */
    public function updateField(int $fieldId, array $data): array
    {
        return $this->update($fieldId, $data);
    }

    /**
     * Delete custom appointment field
     *
     * @param int $fieldId Field ID
     * @return void
     */
    public function deleteField(int $fieldId): void
    {
        $this->delete($fieldId);
    }
}

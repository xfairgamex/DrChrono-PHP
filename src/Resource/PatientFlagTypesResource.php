<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patient Flag Types - Manage custom patient flag definitions
 *
 * Define flag types for categorizing and marking patients with specific
 * attributes, alerts, or conditions (e.g., VIP, high-risk, special needs).
 *
 * API Endpoint: /api/patient_flag_types
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#patient_flag_types
 */
class PatientFlagTypesResource extends AbstractResource
{
    protected string $resourcePath = '/api/patient_flag_types';

    /**
     * List patient flag types
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
     * Get flag types for a specific doctor
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
     * Create patient flag type
     *
     * Required fields:
     * - name (string): Flag type name
     *
     * Optional fields:
     * - color (string): Color code for visual identification
     * - priority (int): Priority level
     *
     * @param array $data Flag type data
     * @return array Created flag type
     */
    public function createFlagType(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update patient flag type
     *
     * @param int $flagTypeId Flag type ID
     * @param array $data Updated data
     * @return array Updated flag type
     */
    public function updateFlagType(int $flagTypeId, array $data): array
    {
        return $this->update($flagTypeId, $data);
    }

    /**
     * Delete patient flag type
     *
     * @param int $flagTypeId Flag type ID
     * @return void
     */
    public function deleteFlagType(int $flagTypeId): void
    {
        $this->delete($flagTypeId);
    }
}

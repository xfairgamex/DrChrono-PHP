<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patients Summary - Bulk patient summary data
 *
 * Get aggregated patient data including visit history, diagnoses, and
 * other clinical summaries in bulk.
 *
 * API Endpoint: /api/patients_summary
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#patients_summary
 */
class PatientsSummaryResource extends AbstractResource
{
    protected string $resourcePath = '/api/patients_summary';

    /**
     * List patient summaries
     *
     * @param array $filters Optional filters:
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Updated since timestamp
     *   - 'verbose' (bool): Include detailed information
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get summaries for patients of a specific doctor
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
     * Get detailed patient summaries with verbose mode
     *
     * @param array $filters Optional filters
     * @return PagedCollection
     */
    public function listDetailed(array $filters = []): PagedCollection
    {
        return $this->listVerbose($filters);
    }

    /**
     * Get a single patient summary
     *
     * @param int $patientId Patient ID
     * @return array Patient summary data
     */
    public function getSummary(int $patientId): array
    {
        return $this->get($patientId);
    }

    /**
     * Get a single patient summary with detailed information
     *
     * @param int $patientId Patient ID
     * @return array Detailed patient summary
     */
    public function getDetailedSummary(int $patientId): array
    {
        return $this->getVerbose($patientId);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Lab Results resource - manage laboratory results
 */
class LabResultsResource extends AbstractResource
{
    protected string $resourcePath = '/api/lab_results';

    /**
     * List lab results for patient
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * List lab results by doctor
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * Get lab result details
     */
    public function getResult(int $resultId): array
    {
        return $this->get($resultId);
    }

    /**
     * Create lab result
     */
    public function createResult(array $resultData): array
    {
        return $this->create($resultData);
    }

    /**
     * Update lab result
     */
    public function updateResult(int $resultId, array $resultData): array
    {
        return $this->update($resultId, $resultData);
    }
}

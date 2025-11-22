<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Problems resource - manage patient problem list
 */
class ProblemsResource extends AbstractResource
{
    protected string $resourcePath = '/api/problems';

    /**
     * List problems for patient
     */
    public function listByPatient(int $patientId): PagedCollection
    {
        return $this->list(['patient' => $patientId]);
    }

    /**
     * Create problem
     */
    public function createProblem(array $problemData): array
    {
        return $this->create($problemData);
    }

    /**
     * Update problem
     */
    public function updateProblem(int $problemId, array $problemData): array
    {
        return $this->update($problemId, $problemData);
    }

    /**
     * Delete problem
     */
    public function deleteProblem(int $problemId): void
    {
        $this->delete($problemId);
    }
}

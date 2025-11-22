<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Allergies resource - manage patient allergies
 */
class AllergiesResource extends AbstractResource
{
    protected string $resourcePath = '/api/allergies';

    /**
     * List allergies for patient
     */
    public function listByPatient(int $patientId): PagedCollection
    {
        return $this->list(['patient' => $patientId]);
    }

    /**
     * Create allergy
     */
    public function createAllergy(array $allergyData): array
    {
        return $this->create($allergyData);
    }

    /**
     * Update allergy
     */
    public function updateAllergy(int $allergyId, array $allergyData): array
    {
        return $this->update($allergyId, $allergyData);
    }

    /**
     * Delete allergy
     */
    public function deleteAllergy(int $allergyId): void
    {
        $this->delete($allergyId);
    }
}

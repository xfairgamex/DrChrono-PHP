<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Immunizations resource - manage patient immunization records
 */
class ImmunizationsResource extends AbstractResource
{
    protected string $resourcePath = '/api/patient_vaccine_records';

    /**
     * List immunizations for patient
     */
    public function listByPatient(int $patientId): PagedCollection
    {
        return $this->list(['patient' => $patientId]);
    }

    /**
     * Create immunization record
     */
    public function createImmunization(array $immunizationData): array
    {
        return $this->create($immunizationData);
    }

    /**
     * Update immunization record
     */
    public function updateImmunization(int $immunizationId, array $immunizationData): array
    {
        return $this->update($immunizationId, $immunizationData);
    }

    /**
     * Delete immunization record
     */
    public function deleteImmunization(int $immunizationId): void
    {
        $this->delete($immunizationId);
    }
}

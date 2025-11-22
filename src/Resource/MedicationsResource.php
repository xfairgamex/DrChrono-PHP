<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Medications resource - manage patient medications
 */
class MedicationsResource extends AbstractResource
{
    protected string $resourcePath = '/api/medications';

    /**
     * List medications for patient
     */
    public function listByPatient(int $patientId): PagedCollection
    {
        return $this->list(['patient' => $patientId]);
    }

    /**
     * Create medication
     */
    public function createMedication(array $medicationData): array
    {
        return $this->create($medicationData);
    }

    /**
     * Update medication
     */
    public function updateMedication(int $medicationId, array $medicationData): array
    {
        return $this->update($medicationId, $medicationData);
    }

    /**
     * Delete medication
     */
    public function deleteMedication(int $medicationId): void
    {
        $this->delete($medicationId);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Prescriptions resource - manage medication prescriptions
 */
class PrescriptionsResource extends AbstractResource
{
    protected string $resourcePath = '/api/prescriptions';

    /**
     * List prescriptions for patient
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * List prescriptions by doctor
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * Create prescription
     */
    public function createPrescription(array $prescriptionData): array
    {
        return $this->create($prescriptionData);
    }

    /**
     * Update prescription
     */
    public function updatePrescription(int $prescriptionId, array $prescriptionData): array
    {
        return $this->update($prescriptionId, $prescriptionData);
    }

    /**
     * Get prescription details
     */
    public function getPrescription(int $prescriptionId): array
    {
        return $this->get($prescriptionId);
    }
}

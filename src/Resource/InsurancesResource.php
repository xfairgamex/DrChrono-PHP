<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Insurances resource - manage patient insurance information
 */
class InsurancesResource extends AbstractResource
{
    protected string $resourcePath = '/api/patient_insurances';

    /**
     * List insurances for patient
     */
    public function listByPatient(int $patientId): PagedCollection
    {
        return $this->list(['patient' => $patientId]);
    }

    /**
     * Get insurance details
     */
    public function getInsurance(int $insuranceId): array
    {
        return $this->get($insuranceId);
    }

    /**
     * Create patient insurance
     */
    public function createInsurance(array $insuranceData): array
    {
        return $this->create($insuranceData);
    }

    /**
     * Update patient insurance
     */
    public function updateInsurance(int $insuranceId, array $insuranceData): array
    {
        return $this->update($insuranceId, $insuranceData);
    }

    /**
     * Delete patient insurance
     */
    public function deleteInsurance(int $insuranceId): void
    {
        $this->delete($insuranceId);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Vitals resource - manage patient vital signs
 */
class VitalsResource extends AbstractResource
{
    protected string $resourcePath = '/api/vitals';

    /**
     * List vitals for patient
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * List vitals by appointment
     */
    public function listByAppointment(int $appointmentId): PagedCollection
    {
        return $this->list(['appointment' => $appointmentId]);
    }

    /**
     * Create vitals record
     */
    public function createVitals(array $vitalsData): array
    {
        return $this->create($vitalsData);
    }

    /**
     * Update vitals record
     */
    public function updateVitals(int $vitalsId, array $vitalsData): array
    {
        return $this->update($vitalsId, $vitalsData);
    }

    /**
     * Delete vitals record
     */
    public function deleteVitals(int $vitalsId): void
    {
        $this->delete($vitalsId);
    }
}

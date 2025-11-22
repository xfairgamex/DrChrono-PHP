<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patients resource - manage patient records
 */
class PatientsResource extends AbstractResource
{
    protected string $resourcePath = '/api/patients';

    /**
     * Search patients by name, DOB, email, etc.
     */
    public function search(array $criteria): PagedCollection
    {
        return $this->list($criteria);
    }

    /**
     * Get patient's summary
     */
    public function getSummary(int $patientId): array
    {
        return $this->httpClient->get("{$this->resourcePath}/{$patientId}/summary");
    }

    /**
     * Get CCDA (Continuity of Care Document)
     */
    public function getCCDA(int $patientId): array
    {
        return $this->httpClient->get("{$this->resourcePath}/{$patientId}/ccda");
    }

    /**
     * Create patient with demographics
     */
    public function createPatient(array $demographics): array
    {
        return $this->create($demographics);
    }

    /**
     * Update patient demographics
     */
    public function updateDemographics(int $patientId, array $demographics): array
    {
        return $this->update($patientId, $demographics);
    }

    /**
     * Get patient's appointment profile
     */
    public function getAppointmentProfile(int $patientId): array
    {
        return $this->httpClient->get("/api/appointment_profiles", ['patient' => $patientId]);
    }

    /**
     * Create custom patient field
     */
    public function setCustomField(int $patientId, string $fieldName, $value): array
    {
        return $this->update($patientId, [$fieldName => $value]);
    }
}

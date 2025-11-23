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

    /**
     * Get patient with full insurance details
     *
     * Requires verbose mode to include:
     * - primary_insurance (full details)
     * - secondary_insurance (full details)
     * - tertiary_insurance (full details)
     * - auto_accident_insurance
     * - workers_comp_insurance
     * - custom_demographics
     * - patient_flags
     * - referring_doctor
     *
     * @param int $patientId Patient ID
     * @return array Patient data with insurance details
     */
    public function getWithInsurance(int $patientId): array
    {
        return $this->getVerbose($patientId);
    }

    /**
     * List patients with insurance details
     *
     * Note: Verbose mode reduces page size to 50 records
     *
     * @param array $filters Optional filters
     * @return PagedCollection
     */
    public function listWithInsurance(array $filters = []): PagedCollection
    {
        return $this->listVerbose($filters);
    }
}

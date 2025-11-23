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

    /**
     * Get or create onpatient access token
     *
     * Generates an access token for the patient to access their information
     * through onpatient portal. This is a special endpoint that either returns
     * an existing token or creates a new one.
     *
     * @param int $patientId Patient ID
     * @return array Access token information
     */
    public function getOnPatientAccess(int $patientId): array
    {
        return $this->httpClient->get("{$this->resourcePath}/{$patientId}/onpatient_access");
    }

    /**
     * Create onpatient access token
     *
     * Creates a new access token for the patient to access their information
     * through onpatient portal.
     *
     * @param int $patientId Patient ID
     * @param array $data Optional token configuration
     * @return array Created access token information
     */
    public function createOnPatientAccess(int $patientId, array $data = []): array
    {
        return $this->httpClient->post("{$this->resourcePath}/{$patientId}/onpatient_access", $data);
    }
}

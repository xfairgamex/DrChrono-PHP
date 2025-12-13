<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patients resource - manage patient records
 *
 * This resource provides methods for managing patient demographics, searching patients,
 * accessing patient summaries, and retrieving insurance information.
 *
 * @see https://app.drchrono.com/api-docs/#tag/Patients Official API documentation
 */
class PatientsResource extends AbstractResource
{
    protected string $resourcePath = '/api/patients';

    /**
     * Search patients by various criteria
     *
     * @param array $criteria Search criteria (first_name, last_name, date_of_birth, email, etc.)
     * @return PagedCollection Paginated patient results
     *
     * @example
     * // Search by name
     * $patients = $client->patients->search([
     *     'first_name' => 'John',
     *     'last_name' => 'Doe',
     * ]);
     *
     * // Search by date of birth
     * $patients = $client->patients->search([
     *     'date_of_birth' => '1980-05-15',
     * ]);
     *
     * // Search by email
     * $patients = $client->patients->search([
     *     'email' => 'john.doe@example.com',
     * ]);
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
     * Create a new patient record
     *
     * @param array $demographics Patient demographic data
     * @return array Created patient data
     *
     * Required fields:
     * - first_name (string): Patient's first name
     * - last_name (string): Patient's last name
     * - gender (string): 'Male', 'Female', or 'Other'
     * - date_of_birth (string): YYYY-MM-DD format
     * - doctor (int): Primary care doctor ID
     *
     * @example
     * $patient = $client->patients->createPatient([
     *     'first_name' => 'John',
     *     'last_name' => 'Doe',
     *     'gender' => 'Male',
     *     'date_of_birth' => '1980-05-15',
     *     'doctor' => 123,
     *     'email' => 'john.doe@example.com',
     *     'cell_phone' => '555-0123',
     *     'address' => '123 Main St',
     *     'city' => 'Springfield',
     *     'state' => 'IL',
     *     'zip_code' => '62701',
     * ]);
     *
     * echo "Created patient ID: {$patient['id']}\n";
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
     * Get patient with full insurance details (verbose mode)
     *
     * Uses verbose mode to include additional fields that require extra database queries:
     * - primary_insurance: Complete primary insurance information
     * - secondary_insurance: Complete secondary insurance information
     * - tertiary_insurance: Complete tertiary insurance information
     * - auto_accident_insurance: Auto accident insurance details
     * - workers_comp_insurance: Workers' compensation insurance details
     * - custom_demographics: All custom demographic fields
     * - patient_flags: Patient flag details and metadata
     * - patient_flags_attached: Currently attached patient flags
     * - referring_doctor: Complete referring physician information
     *
     * @param int $patientId Patient ID
     * @return array Patient data with insurance details
     *
     * @example
     * $patient = $client->patients->getWithInsurance(12345);
     *
     * // Access primary insurance
     * if (isset($patient['primary_insurance'])) {
     *     $insurance = $patient['primary_insurance'];
     *     echo "Payer: {$insurance['insurance_payer_name']}\n";
     *     echo "Plan: {$insurance['insurance_plan_name']}\n";
     *     echo "Policy #: {$insurance['insurance_id_number']}\n";
     *     echo "Group #: {$insurance['insurance_group_number']}\n";
     * }
     *
     * // Check for workers' comp
     * if (isset($patient['workers_comp_insurance'])) {
     *     echo "Workers Comp Claim: {$patient['workers_comp_insurance']['claim_number']}\n";
     * }
     *
     * // View custom demographics
     * foreach ($patient['custom_demographics'] ?? [] as $field) {
     *     echo "{$field['field_name']}: {$field['field_value']}\n";
     * }
     *
     * @see https://github.com/drchrono/php-sdk/blob/main/docs/VERBOSE_MODE.md Verbose Mode Guide
     */
    public function getWithInsurance(int $patientId): array
    {
        return $this->getVerbose($patientId);
    }

    /**
     * List patients with insurance details (verbose mode)
     *
     * Returns patients with additional insurance and demographic fields.
     * See getWithInsurance() for the list of included verbose fields.
     *
     * Performance Note: Page size is reduced from 250 to 50 records in verbose mode.
     * Each record requires additional database queries.
     *
     * @param array $filters Optional filters (doctor, since, date_of_birth, etc.)
     * @return PagedCollection Paginated results with verbose patient data
     *
     * @example
     * // Get all patients for a doctor with insurance details
     * $patients = $client->patients->listWithInsurance(['doctor' => 123]);
     *
     * foreach ($patients as $patient) {
     *     echo "{$patient['first_name']} {$patient['last_name']}\n";
     *
     *     // Check insurance coverage
     *     if (isset($patient['primary_insurance'])) {
     *         echo "  Insurance: {$patient['primary_insurance']['insurance_payer_name']}\n";
     *     } else {
     *         echo "  No insurance on file\n";
     *     }
     *
     *     // Check for patient flags
     *     if (!empty($patient['patient_flags_attached'])) {
     *         echo "  Flags: " . count($patient['patient_flags_attached']) . "\n";
     *     }
     * }
     *
     * @see https://github.com/drchrono/php-sdk/blob/main/docs/VERBOSE_MODE.md Verbose Mode Guide
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

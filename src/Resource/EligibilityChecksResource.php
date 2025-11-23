<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Eligibility Checks - Insurance eligibility verification
 *
 * Verify patient insurance coverage and benefits through real-time
 * eligibility checks with insurance payers.
 *
 * API Endpoint: /api/eligibility_checks
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#eligibility_checks
 */
class EligibilityChecksResource extends AbstractResource
{
    protected string $resourcePath = '/api/eligibility_checks';

    /**
     * List eligibility checks
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'appointment' (int): Filter by appointment ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Updated since timestamp
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get eligibility checks for a specific patient
     *
     * @param int $patientId Patient ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * Get eligibility checks for a specific appointment
     *
     * @param int $appointmentId Appointment ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByAppointment(int $appointmentId, array $filters = []): PagedCollection
    {
        $filters['appointment'] = $appointmentId;
        return $this->list($filters);
    }

    /**
     * Create and run eligibility check
     *
     * Required fields:
     * - patient (int): Patient ID
     * - appointment (int): Appointment ID
     *
     * Optional fields:
     * - insurance (string): Insurance type ('primary', 'secondary', 'tertiary')
     * - service_type (string): Type of service being verified
     *
     * @param array $data Eligibility check data
     * @return array Created eligibility check with results
     */
    public function verify(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Check primary insurance eligibility
     *
     * @param int $patientId Patient ID
     * @param int $appointmentId Appointment ID
     * @param array $additionalData Additional data for the check
     * @return array Eligibility check result
     */
    public function verifyPrimaryInsurance(int $patientId, int $appointmentId, array $additionalData = []): array
    {
        $data = [
            'patient' => $patientId,
            'appointment' => $appointmentId,
            'insurance' => 'primary',
        ];

        return $this->verify(array_merge($data, $additionalData));
    }

    /**
     * Check secondary insurance eligibility
     *
     * @param int $patientId Patient ID
     * @param int $appointmentId Appointment ID
     * @param array $additionalData Additional data for the check
     * @return array Eligibility check result
     */
    public function verifySecondaryInsurance(int $patientId, int $appointmentId, array $additionalData = []): array
    {
        $data = [
            'patient' => $patientId,
            'appointment' => $appointmentId,
            'insurance' => 'secondary',
        ];

        return $this->verify(array_merge($data, $additionalData));
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patient Risk Assessments - Document patient risk evaluation assessments
 *
 * Risk assessments identify and evaluate potential health risks,
 * enabling proactive care and preventive interventions.
 *
 * API Endpoint: /api/patient_risk_assessments
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#patient_risk_assessments
 */
class PatientRiskAssessmentsResource extends AbstractResource
{
    protected string $resourcePath = '/api/patient_risk_assessments';

    /**
     * List patient risk assessments
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Filter by update date
     *   - 'date' (string): Filter by assessment date
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific risk assessment
     *
     * @param int|string $assessmentId Assessment ID
     * @return array Assessment data
     */
    public function get(int|string $assessmentId): array
    {
        return parent::get($assessmentId);
    }

    /**
     * List risk assessments for a specific patient
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
     * List risk assessments by doctor
     *
     * @param int $doctorId Doctor ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * Create a new risk assessment
     *
     * Required fields:
     * - patient (int): Patient ID
     * - assessment_type (string): Type of assessment
     * - date (string): Assessment date (YYYY-MM-DD)
     *
     * Optional fields:
     * - doctor (int): Doctor ID
     * - risk_level (string): Overall risk level (low, medium, high)
     * - risk_factors (array): Identified risk factors
     * - recommendations (string): Risk mitigation recommendations
     * - score (int): Numerical risk score
     * - notes (string): Additional notes
     *
     * @param array $data Assessment data
     * @return array Created assessment
     */
    public function createAssessment(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing risk assessment
     *
     * @param int $assessmentId Assessment ID
     * @param array $data Updated data
     * @return array Updated assessment
     */
    public function updateAssessment(int $assessmentId, array $data): array
    {
        return $this->update($assessmentId, $data);
    }

    /**
     * Delete a risk assessment
     *
     * @param int $assessmentId Assessment ID
     * @return void
     */
    public function deleteAssessment(int $assessmentId): void
    {
        $this->delete($assessmentId);
    }

    /**
     * Get the most recent assessment for a patient
     *
     * @param int $patientId Patient ID
     * @param string|null $assessmentType Optional type filter
     * @return array|null Most recent assessment or null
     */
    public function getMostRecent(int $patientId, ?string $assessmentType = null): ?array
    {
        $filters = [];
        if ($assessmentType !== null) {
            $filters['assessment_type'] = $assessmentType;
        }

        $assessments = $this->listByPatient($patientId, $filters);
        $items = iterator_to_array($assessments);

        if (empty($items)) {
            return null;
        }

        // Sort by date descending and return first
        usort($items, function ($a, $b) {
            return strtotime($b['date'] ?? '0') - strtotime($a['date'] ?? '0');
        });

        return $items[0];
    }

    /**
     * Get high-risk patients
     *
     * Returns assessments marked as high risk
     *
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function getHighRisk(array $filters = []): PagedCollection
    {
        $filters['risk_level'] = 'high';
        return $this->list($filters);
    }

    /**
     * Get assessments within a date range
     *
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByDateRange(string $startDate, string $endDate, array $filters = []): PagedCollection
    {
        $filters['date_range'] = "{$startDate}/{$endDate}";
        return $this->list($filters);
    }
}

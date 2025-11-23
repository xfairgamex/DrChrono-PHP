<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patient Interventions - Track clinical interventions and treatments
 *
 * Interventions record specific clinical actions taken to address
 * patient health issues, monitor progress, and achieve care goals.
 *
 * API Endpoint: /api/patient_interventions
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#patient_interventions
 */
class PatientInterventionsResource extends AbstractResource
{
    protected string $resourcePath = '/api/patient_interventions';

    /**
     * List patient interventions
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'care_plan' (int): Filter by care plan ID
     *   - 'since' (string): Filter by update date
     *   - 'status' (string): Filter by status
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific intervention
     *
     * @param int|string $interventionId Intervention ID
     * @return array Intervention data
     */
    public function get(int|string $interventionId): array
    {
        return parent::get($interventionId);
    }

    /**
     * List interventions for a specific patient
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
     * List interventions by doctor
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
     * List interventions by care plan
     *
     * @param int $carePlanId Care plan ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByCarePlan(int $carePlanId, array $filters = []): PagedCollection
    {
        $filters['care_plan'] = $carePlanId;
        return $this->list($filters);
    }

    /**
     * Create a new intervention
     *
     * Required fields:
     * - patient (int): Patient ID
     * - intervention_type (string): Type of intervention
     * - description (string): Intervention description
     *
     * Optional fields:
     * - doctor (int): Doctor ID
     * - care_plan (int): Associated care plan ID
     * - start_date (string): Start date (YYYY-MM-DD)
     * - end_date (string): End date (YYYY-MM-DD)
     * - frequency (string): How often intervention is performed
     * - status (string): Status (active, completed, discontinued)
     * - outcome (string): Intervention outcome
     * - notes (string): Additional notes
     *
     * @param array $data Intervention data
     * @return array Created intervention
     */
    public function createIntervention(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing intervention
     *
     * @param int $interventionId Intervention ID
     * @param array $data Updated data
     * @return array Updated intervention
     */
    public function updateIntervention(int $interventionId, array $data): array
    {
        return $this->update($interventionId, $data);
    }

    /**
     * Delete an intervention
     *
     * @param int $interventionId Intervention ID
     * @return void
     */
    public function deleteIntervention(int $interventionId): void
    {
        $this->delete($interventionId);
    }

    /**
     * Get active interventions for a patient
     *
     * @param int $patientId Patient ID
     * @return PagedCollection
     */
    public function getActiveForPatient(int $patientId): PagedCollection
    {
        return $this->listByPatient($patientId, ['status' => 'active']);
    }

    /**
     * Mark intervention as completed
     *
     * @param int $interventionId Intervention ID
     * @param string $outcome Outcome description
     * @return array Updated intervention
     */
    public function markCompleted(int $interventionId, string $outcome): array
    {
        return $this->updateIntervention($interventionId, [
            'status' => 'completed',
            'outcome' => $outcome,
            'end_date' => date('Y-m-d'),
        ]);
    }

    /**
     * Discontinue an intervention
     *
     * @param int $interventionId Intervention ID
     * @param string $reason Reason for discontinuation
     * @return array Updated intervention
     */
    public function discontinue(int $interventionId, string $reason): array
    {
        return $this->updateIntervention($interventionId, [
            'status' => 'discontinued',
            'discontinuation_reason' => $reason,
            'end_date' => date('Y-m-d'),
        ]);
    }

    /**
     * Get interventions by type
     *
     * @param string $interventionType Type of intervention
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function getByType(string $interventionType, array $filters = []): PagedCollection
    {
        $filters['intervention_type'] = $interventionType;
        return $this->list($filters);
    }
}

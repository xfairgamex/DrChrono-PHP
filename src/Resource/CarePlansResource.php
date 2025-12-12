<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Care Plans - Manage patient care plans and treatment strategies
 *
 * Care plans define coordinated treatment strategies, goals, and interventions
 * for managing patient health conditions over time.
 *
 * API Endpoint: /api/care_plans
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#care_plans
 */
class CarePlansResource extends AbstractResource
{
    protected string $resourcePath = '/api/care_plans';

    /**
     * List care plans
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Filter by update date
     *   - 'status' (string): Filter by status (active, completed, cancelled)
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific care plan
     *
     * @param int|string $carePlanId Care plan ID
     * @return array Care plan data
     */
    public function get(int|string $carePlanId): array
    {
        return parent::get($carePlanId);
    }

    /**
     * List care plans for a specific patient
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
     * List care plans by doctor
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
     * Create a new care plan
     *
     * Required fields:
     * - patient (int): Patient ID
     * - title (string): Care plan title
     *
     * Optional fields:
     * - doctor (int): Doctor ID
     * - description (string): Care plan description
     * - goals (array): Treatment goals
     * - interventions (array): Planned interventions
     * - start_date (string): Start date (YYYY-MM-DD)
     * - end_date (string): End date (YYYY-MM-DD)
     * - status (string): Status (active, completed, cancelled)
     *
     * @param array $data Care plan data
     * @return array Created care plan
     */
    public function createCarePlan(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing care plan
     *
     * @param int $carePlanId Care plan ID
     * @param array $data Updated data
     * @return array Updated care plan
     */
    public function updateCarePlan(int $carePlanId, array $data): array
    {
        return $this->update($carePlanId, $data);
    }

    /**
     * Delete a care plan
     *
     * @param int $carePlanId Care plan ID
     * @return void
     */
    public function deleteCarePlan(int $carePlanId): void
    {
        $this->delete($carePlanId);
    }

    /**
     * Get active care plans for a patient
     *
     * Returns only currently active care plans
     *
     * @param int $patientId Patient ID
     * @return PagedCollection
     */
    public function getActiveForPatient(int $patientId): PagedCollection
    {
        return $this->listByPatient($patientId, ['status' => 'active']);
    }

    /**
     * Mark care plan as completed
     *
     * @param int $carePlanId Care plan ID
     * @param string|null $completionDate Completion date (defaults to today)
     * @return array Updated care plan
     */
    public function markCompleted(int $carePlanId, ?string $completionDate = null): array
    {
        return $this->updateCarePlan($carePlanId, [
            'status' => 'completed',
            'end_date' => $completionDate ?? date('Y-m-d'),
        ]);
    }

    /**
     * Cancel a care plan
     *
     * @param int $carePlanId Care plan ID
     * @param string $reason Cancellation reason
     * @return array Updated care plan
     */
    public function cancel(int $carePlanId, string $reason): array
    {
        return $this->updateCarePlan($carePlanId, [
            'status' => 'cancelled',
            'cancellation_reason' => $reason,
        ]);
    }

    /**
     * Add a goal to a care plan
     *
     * @param int $carePlanId Care plan ID
     * @param array $goal Goal data (description, target_date, etc.)
     * @return array Updated care plan
     */
    public function addGoal(int $carePlanId, array $goal): array
    {
        $carePlan = $this->get($carePlanId);
        $goals = $carePlan['goals'] ?? [];
        $goals[] = $goal;

        return $this->updateCarePlan($carePlanId, ['goals' => $goals]);
    }
}

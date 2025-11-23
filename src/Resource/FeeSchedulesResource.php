<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Fee Schedules - Manage pricing and fee schedules
 *
 * Fee schedules define pricing for procedures and services. They can be associated
 * with specific insurance plans or used as default pricing for the practice.
 *
 * API Endpoint: /api/fee_schedules
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#fee_schedules
 */
class FeeSchedulesResource extends AbstractResource
{
    protected string $resourcePath = '/api/fee_schedules';

    /**
     * List fee schedules
     *
     * @param array $filters Optional filters:
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Filter by update date
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific fee schedule
     *
     * @param int|string $scheduleId Fee schedule ID
     * @return array Fee schedule data
     */
    public function get(int|string $scheduleId): array
    {
        return parent::get($scheduleId);
    }

    /**
     * List fee schedules for a specific doctor
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
     * Create a new fee schedule
     *
     * Required fields:
     * - name (string): Schedule name
     * - code (string): Procedure/service code
     * - price (float): Fee amount
     *
     * Optional fields:
     * - doctor (int): Doctor ID
     * - insurance_plan (int): Associated insurance plan
     * - modifiers (array): Procedure modifiers
     *
     * @param array $data Fee schedule data
     * @return array Created fee schedule
     */
    public function createSchedule(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing fee schedule
     *
     * @param int $scheduleId Fee schedule ID
     * @param array $data Updated data
     * @return array Updated fee schedule
     */
    public function updateSchedule(int $scheduleId, array $data): array
    {
        return $this->update($scheduleId, $data);
    }

    /**
     * Delete a fee schedule
     *
     * @param int $scheduleId Fee schedule ID
     * @return void
     */
    public function deleteSchedule(int $scheduleId): void
    {
        $this->delete($scheduleId);
    }

    /**
     * Get fee for a specific procedure code
     *
     * @param string $code Procedure code
     * @param array $filters Additional filters (doctor, insurance_plan)
     * @return PagedCollection
     */
    public function getByCode(string $code, array $filters = []): PagedCollection
    {
        $filters['code'] = $code;
        return $this->list($filters);
    }
}

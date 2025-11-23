<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Custom Insurance Plan Names - Manage custom insurance plan naming
 *
 * Allows practices to customize how insurance plan names are displayed
 * in the system, providing clearer or more specific naming conventions.
 *
 * API Endpoint: /api/custom_insurance_plan_names
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#custom_insurance_plan_names
 */
class CustomInsurancePlanNamesResource extends AbstractResource
{
    protected string $resourcePath = '/api/custom_insurance_plan_names';

    /**
     * List custom insurance plan names
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
     * Get a specific custom insurance plan name
     *
     * @param int|string $planId Custom plan name ID
     * @return array Custom plan name data
     */
    public function get(int|string $planId): array
    {
        return parent::get($planId);
    }

    /**
     * List custom plan names for a specific doctor
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
     * Create a new custom insurance plan name
     *
     * Required fields:
     * - insurance_plan (int): Original insurance plan ID
     * - custom_name (string): Custom display name
     *
     * Optional fields:
     * - doctor (int): Doctor ID
     * - notes (string): Additional notes
     *
     * @param array $data Custom plan name data
     * @return array Created custom plan name
     */
    public function createPlanName(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing custom insurance plan name
     *
     * @param int $planId Custom plan name ID
     * @param array $data Updated data
     * @return array Updated custom plan name
     */
    public function updatePlanName(int $planId, array $data): array
    {
        return $this->update($planId, $data);
    }

    /**
     * Delete a custom insurance plan name
     *
     * @param int $planId Custom plan name ID
     * @return void
     */
    public function deletePlanName(int $planId): void
    {
        $this->delete($planId);
    }

    /**
     * Set custom name for an insurance plan
     *
     * Convenience method to quickly set a custom name for an insurance plan
     *
     * @param int $insurancePlanId Original insurance plan ID
     * @param string $customName Custom display name
     * @param int|null $doctorId Optional doctor ID
     * @return array Created custom plan name
     */
    public function setCustomName(int $insurancePlanId, string $customName, ?int $doctorId = null): array
    {
        $data = [
            'insurance_plan' => $insurancePlanId,
            'custom_name' => $customName,
        ];

        if ($doctorId !== null) {
            $data['doctor'] = $doctorId;
        }

        return $this->createPlanName($data);
    }
}

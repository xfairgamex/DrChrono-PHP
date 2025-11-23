<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Billing Profiles - Manage billing configurations
 *
 * Billing profiles contain settings for billing, claims, and payment processing
 * including tax IDs, billing addresses, and claim defaults.
 *
 * API Endpoint: /api/billing_profiles
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#billing_profiles
 */
class BillingProfilesResource extends AbstractResource
{
    protected string $resourcePath = '/api/billing_profiles';

    /**
     * List billing profiles
     *
     * @param array $filters Optional filters:
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'practice' (int): Filter by practice ID
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get billing profile for a specific doctor
     *
     * @param int $doctorId Doctor ID
     * @return array|null Billing profile or null if not found
     */
    public function getByDoctor(int $doctorId): ?array
    {
        $profiles = $this->list(['doctor' => $doctorId]);
        $items = $profiles->getItems();
        return !empty($items) ? $items[0] : null;
    }

    /**
     * Create billing profile
     *
     * Required fields vary by practice setup. Common fields:
     * - doctor (int): Doctor ID
     * - npi (string): National Provider Identifier
     * - tax_id (string): Tax identification number
     *
     * @param array $data Profile data
     * @return array Created profile
     */
    public function createProfile(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update billing profile
     *
     * @param int $profileId Profile ID
     * @param array $data Updated data
     * @return array Updated profile
     */
    public function updateProfile(int $profileId, array $data): array
    {
        return $this->update($profileId, $data);
    }
}

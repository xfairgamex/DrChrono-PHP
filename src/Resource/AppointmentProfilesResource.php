<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Appointment Profiles - Manage appointment types with standard durations
 *
 * Appointment profiles define the types of appointments available in the practice,
 * including default duration, color coding, and scheduling rules.
 *
 * API Endpoint: /api/appointment_profiles
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#appointment_profiles
 */
class AppointmentProfilesResource extends AbstractResource
{
    protected string $resourcePath = '/api/appointment_profiles';

    /**
     * List appointment profiles
     *
     * @param array $filters Optional filters:
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'office' (int): Filter by office ID
     *   - 'archived' (bool): Include archived profiles
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get appointment profiles for a specific doctor
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
     * Get appointment profiles for a specific office
     *
     * @param int $officeId Office ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByOffice(int $officeId, array $filters = []): PagedCollection
    {
        $filters['office'] = $officeId;
        return $this->list($filters);
    }

    /**
     * Create appointment profile
     *
     * Required fields:
     * - name (string): Profile name
     * - duration (int): Duration in minutes
     * - doctor (int): Doctor ID
     *
     * @param array $data Profile data
     * @return array Created profile
     */
    public function createProfile(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update appointment profile
     *
     * @param int $profileId Profile ID
     * @param array $data Updated data
     * @return array Updated profile
     */
    public function updateProfile(int $profileId, array $data): array
    {
        return $this->update($profileId, $data);
    }

    /**
     * Archive appointment profile
     *
     * @param int $profileId Profile ID
     * @return array Archived profile
     */
    public function archive(int $profileId): array
    {
        return $this->update($profileId, ['archived' => true]);
    }
}

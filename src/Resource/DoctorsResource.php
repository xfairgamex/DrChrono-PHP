<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Doctors Resource - Provider-specific information
 *
 * Retrieve doctor/provider information within your practice group.
 * This is a read-only endpoint - doctors cannot be created or modified via API.
 *
 * API Endpoint: /api/doctors
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#doctors
 *
 * Use Cases:
 * - Display provider directories
 * - Provider selection in scheduling interfaces
 * - Provider filtering for appointments and clinical data
 * - Practice group management
 */
class DoctorsResource extends AbstractResource
{
    protected string $resourcePath = '/api/doctors';

    /**
     * List all doctors in the practice group
     *
     * Returns basic provider information for all doctors in your practice group.
     * This endpoint is read-only and does not support filtering parameters.
     *
     * @param array $filters Optional filters (none documented for this endpoint)
     * @return PagedCollection Collection of doctor records
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific doctor by ID
     *
     * @param int|string $doctorId Doctor ID
     * @return array Doctor information
     */
    public function get(int|string $doctorId): array
    {
        return parent::get($doctorId);
    }

    /**
     * Get all active (non-suspended) doctors
     *
     * Filters the list to return only doctors whose accounts are not suspended.
     *
     * @return array|\Illuminate\Support\Collection List of active doctors
     */
    public function listActive()
    {
        $collection = $this->list();

        return $collection->filterItems(function (array $doctor): bool {
            return !($doctor['is_account_suspended'] ?? false);
        });
    }

    /**
     * Get all suspended doctors
     *
     * Filters the list to return only doctors whose accounts are suspended.
     *
     * @return array|\Illuminate\Support\Collection List of suspended doctors
     */
    public function listSuspended()
    {
        $collection = $this->list();

        return $collection->filterItems(function (array $doctor): bool {
            return (bool)($doctor['is_account_suspended'] ?? false);
        });
    }

    /**
     * Get doctors by specialty
     *
     * Filters doctors by their specialty (e.g., "Cardiology", "Pediatrics").
     *
     * @param string $specialty Specialty to filter by
     * @return array|\Illuminate\Support\Collection List of doctors matching the specialty
     */
    public function listBySpecialty(string $specialty)
    {
        $collection = $this->list();

        return $collection->filterItems(function (array $doctor) use ($specialty): bool {
            return ($doctor['specialty'] ?? '') === $specialty;
        });
    }

    /**
     * Search doctors by name
     *
     * Searches for doctors by first name, last name, or full name.
     * Case-insensitive partial matching.
     *
     * @param string $searchTerm Search term (name)
     * @return array|\Illuminate\Support\Collection List of matching doctors
     */
    public function search(string $searchTerm)
    {
        $searchTerm = strtolower($searchTerm);
        $collection = $this->list();

        return $collection->filterItems(function (array $doctor) use ($searchTerm): bool {
            $firstName = strtolower($doctor['first_name'] ?? '');
            $lastName = strtolower($doctor['last_name'] ?? '');
            $fullName = $firstName . ' ' . $lastName;

            return str_contains($firstName, $searchTerm)
                || str_contains($lastName, $searchTerm)
                || str_contains($fullName, $searchTerm);
        });
    }

    /**
     * Get doctors by practice group
     *
     * Filters doctors by practice group ID.
     *
     * @param int $practiceGroupId Practice group ID
     * @return array|\Illuminate\Support\Collection List of doctors in the practice group
     */
    public function listByPracticeGroup(int $practiceGroupId)
    {
        $collection = $this->list();

        return $collection->filterItems(function (array $doctor) use ($practiceGroupId): bool {
            return ($doctor['practice_group'] ?? null) === $practiceGroupId;
        });
    }

    /**
     * Get doctor's full name
     *
     * Returns formatted full name for a doctor record.
     *
     * @param array $doctor Doctor record
     * @return string Formatted name (e.g., "John Smith, MD")
     */
    public function getFullName(array $doctor): string
    {
        $name = trim(($doctor['first_name'] ?? '') . ' ' . ($doctor['last_name'] ?? ''));

        if (!empty($doctor['suffix'])) {
            $name .= ', ' . $doctor['suffix'];
        }

        return $name;
    }

    /**
     * Check if a doctor is active (not suspended)
     *
     * @param array $doctor Doctor record
     * @return bool True if active, false if suspended
     */
    public function isActive(array $doctor): bool
    {
        return !($doctor['is_account_suspended'] ?? false);
    }
}

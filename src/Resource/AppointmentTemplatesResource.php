<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Appointment Templates - Manage recurring appointment blocks
 *
 * Appointment templates allow practices to define recurring availability blocks
 * for scheduling, including regular office hours and special clinic sessions.
 *
 * API Endpoint: /api/appointment_templates
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#appointment_templates
 */
class AppointmentTemplatesResource extends AbstractResource
{
    protected string $resourcePath = '/api/appointment_templates';

    /**
     * List appointment templates
     *
     * @param array $filters Optional filters:
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'office' (int): Filter by office ID
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get templates for a specific doctor
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
     * Get templates for a specific office
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
     * Create appointment template
     *
     * Required fields:
     * - doctor (int): Doctor ID
     * - office (int): Office ID
     * - profile (int): Appointment profile ID
     * - day_of_week (int): Day of week (0-6, Sunday=0)
     * - start_time (string): Start time (HH:MM format)
     * - duration (int): Duration in minutes
     *
     * @param array $data Template data
     * @return array Created template
     */
    public function createTemplate(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update appointment template
     *
     * @param int $templateId Template ID
     * @param array $data Updated data
     * @return array Updated template
     */
    public function updateTemplate(int $templateId, array $data): array
    {
        return $this->update($templateId, $data);
    }

    /**
     * Delete appointment template
     *
     * @param int $templateId Template ID
     * @return void
     */
    public function deleteTemplate(int $templateId): void
    {
        $this->delete($templateId);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Procedures - Record medical procedures performed
 *
 * Procedures track medical interventions, surgical procedures, and other
 * clinical actions performed on patients.
 *
 * API Endpoint: /api/procedures
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#procedures
 */
class ProceduresResource extends AbstractResource
{
    protected string $resourcePath = '/api/procedures';

    /**
     * List procedures
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'appointment' (int): Filter by appointment ID
     *   - 'since' (string): Filter by update date
     *   - 'date' (string): Filter by procedure date
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific procedure
     *
     * @param int|string $procedureId Procedure ID
     * @return array Procedure data
     */
    public function get(int|string $procedureId): array
    {
        return parent::get($procedureId);
    }

    /**
     * List procedures for a specific patient
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
     * List procedures for a specific doctor
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
     * List procedures for a specific appointment
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
     * Create a new procedure record
     *
     * Required fields:
     * - patient (int): Patient ID
     * - code (string): Procedure code (CPT/HCPCS)
     * - description (string): Procedure description
     * - date (string): Date performed (YYYY-MM-DD)
     *
     * Optional fields:
     * - doctor (int): Performing doctor ID
     * - appointment (int): Associated appointment ID
     * - notes (string): Procedure notes
     * - status (string): Procedure status
     *
     * @param array $data Procedure data
     * @return array Created procedure
     */
    public function createProcedure(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing procedure
     *
     * @param int $procedureId Procedure ID
     * @param array $data Updated data
     * @return array Updated procedure
     */
    public function updateProcedure(int $procedureId, array $data): array
    {
        return $this->update($procedureId, $data);
    }

    /**
     * Delete a procedure
     *
     * @param int $procedureId Procedure ID
     * @return void
     */
    public function deleteProcedure(int $procedureId): void
    {
        $this->delete($procedureId);
    }

    /**
     * Get procedures by code
     *
     * Find all procedures with a specific CPT/HCPCS code
     *
     * @param string $code Procedure code
     * @param array $filters Additional filters
     * @return array Matching procedures
     */
    public function getByCode(string $code, array $filters = []): array
    {
        $procedures = $this->list($filters);
        $matching = [];

        foreach ($procedures as $procedure) {
            if (($procedure['code'] ?? '') === $code) {
                $matching[] = $procedure;
            }
        }

        return $matching;
    }

    /**
     * Get procedures within a date range
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

<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patient Communications - Manage communications with patients
 *
 * Patient communications track care team interactions with patients,
 * including educational materials, follow-up instructions, and care coordination.
 *
 * API Endpoint: /api/patient_communications
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#patient_communications
 */
class PatientCommunicationsResource extends AbstractResource
{
    protected string $resourcePath = '/api/patient_communications';

    /**
     * List patient communications
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Filter by update date
     *   - 'date' (string): Filter by communication date
     *   - 'type' (string): Filter by communication type
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific communication
     *
     * @param int|string $communicationId Communication ID
     * @return array Communication data
     */
    public function get(int|string $communicationId): array
    {
        return parent::get($communicationId);
    }

    /**
     * List communications for a specific patient
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
     * List communications by doctor
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
     * Create a new communication record
     *
     * Required fields:
     * - patient (int): Patient ID
     * - communication_type (string): Type of communication
     * - date (string): Communication date (YYYY-MM-DD)
     *
     * Optional fields:
     * - doctor (int): Doctor ID
     * - subject (string): Communication subject
     * - content (string): Communication content
     * - method (string): Communication method (phone, email, portal, in-person)
     * - recipient (string): Communication recipient
     * - purpose (string): Purpose of communication
     * - outcome (string): Communication outcome
     * - follow_up_required (bool): Whether follow-up is needed
     * - notes (string): Additional notes
     *
     * @param array $data Communication data
     * @return array Created communication
     */
    public function createCommunication(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing communication
     *
     * @param int $communicationId Communication ID
     * @param array $data Updated data
     * @return array Updated communication
     */
    public function updateCommunication(int $communicationId, array $data): array
    {
        return $this->update($communicationId, $data);
    }

    /**
     * Delete a communication
     *
     * @param int $communicationId Communication ID
     * @return void
     */
    public function deleteCommunication(int $communicationId): void
    {
        $this->delete($communicationId);
    }

    /**
     * Get communications requiring follow-up
     *
     * @param array $filters Additional filters
     * @return array Communications requiring follow-up
     */
    public function getRequiringFollowUp(array $filters = []): array
    {
        $communications = $this->list($filters);
        $followUps = [];

        foreach ($communications as $communication) {
            if (!empty($communication['follow_up_required'])) {
                $followUps[] = $communication;
            }
        }

        return $followUps;
    }

    /**
     * Get communications by type
     *
     * @param string $type Communication type
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function getByType(string $type, array $filters = []): PagedCollection
    {
        $filters['type'] = $type;
        return $this->list($filters);
    }

    /**
     * Get communications by method
     *
     * @param string $method Communication method (phone, email, portal, in-person)
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function getByMethod(string $method, array $filters = []): PagedCollection
    {
        $filters['method'] = $method;
        return $this->list($filters);
    }

    /**
     * Get communications within a date range
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

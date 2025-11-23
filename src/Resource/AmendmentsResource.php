<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Amendments - Manage medical record amendments and corrections
 *
 * Amendments allow modification of clinical records after initial creation,
 * maintaining an audit trail of changes for compliance and accuracy.
 *
 * API Endpoint: /api/amendments
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#amendments
 */
class AmendmentsResource extends AbstractResource
{
    protected string $resourcePath = '/api/amendments';

    /**
     * List amendments
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Filter by update date
     *   - 'status' (string): Filter by status
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific amendment
     *
     * @param int|string $amendmentId Amendment ID
     * @return array Amendment data
     */
    public function get(int|string $amendmentId): array
    {
        return parent::get($amendmentId);
    }

    /**
     * List amendments for a specific patient
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
     * List amendments by doctor
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
     * Create a new amendment
     *
     * Required fields:
     * - patient (int): Patient ID
     * - description (string): Amendment description
     * - reason (string): Reason for amendment
     *
     * Optional fields:
     * - doctor (int): Doctor ID
     * - clinical_note (int): Clinical note being amended
     * - status (string): Amendment status (pending, approved, denied)
     * - requested_by (string): Person requesting amendment
     *
     * @param array $data Amendment data
     * @return array Created amendment
     */
    public function createAmendment(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing amendment
     *
     * @param int $amendmentId Amendment ID
     * @param array $data Updated data
     * @return array Updated amendment
     */
    public function updateAmendment(int $amendmentId, array $data): array
    {
        return $this->update($amendmentId, $data);
    }

    /**
     * Delete an amendment
     *
     * @param int $amendmentId Amendment ID
     * @return void
     */
    public function deleteAmendment(int $amendmentId): void
    {
        $this->delete($amendmentId);
    }

    /**
     * Approve an amendment
     *
     * Mark amendment as approved and apply changes
     *
     * @param int $amendmentId Amendment ID
     * @param string|null $approverNotes Optional notes from approver
     * @return array Updated amendment
     */
    public function approve(int $amendmentId, ?string $approverNotes = null): array
    {
        $data = ['status' => 'approved'];

        if ($approverNotes !== null) {
            $data['approver_notes'] = $approverNotes;
        }

        return $this->updateAmendment($amendmentId, $data);
    }

    /**
     * Deny an amendment
     *
     * Mark amendment as denied with reason
     *
     * @param int $amendmentId Amendment ID
     * @param string $denialReason Reason for denial
     * @return array Updated amendment
     */
    public function deny(int $amendmentId, string $denialReason): array
    {
        return $this->updateAmendment($amendmentId, [
            'status' => 'denied',
            'denial_reason' => $denialReason,
        ]);
    }

    /**
     * Get pending amendments
     *
     * Retrieve amendments awaiting approval
     *
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function getPending(array $filters = []): PagedCollection
    {
        $filters['status'] = 'pending';
        return $this->list($filters);
    }

    /**
     * Get amendment history for a clinical note
     *
     * @param int $clinicalNoteId Clinical note ID
     * @return array Amendments for this note
     */
    public function getHistoryForNote(int $clinicalNoteId): array
    {
        $amendments = $this->list(['clinical_note' => $clinicalNoteId]);
        return iterator_to_array($amendments);
    }
}

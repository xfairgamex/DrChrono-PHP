<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patient Messages - Manage patient communications
 *
 * Send and receive messages with patients through the patient portal,
 * including secure messaging and appointment reminders.
 *
 * API Endpoint: /api/patient_messages
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#patient_messages
 */
class PatientMessagesResource extends AbstractResource
{
    protected string $resourcePath = '/api/patient_messages';

    /**
     * List patient messages
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Updated since timestamp
     *   - 'read' (bool): Filter by read status
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get messages for a specific patient
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
     * Get unread messages
     *
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listUnread(array $filters = []): PagedCollection
    {
        $filters['read'] = false;
        return $this->list($filters);
    }

    /**
     * Send message to patient
     *
     * Required fields:
     * - patient (int): Patient ID
     * - doctor (int): Doctor ID
     * - message (string): Message content
     *
     * @param array $data Message data
     * @return array Created message
     */
    public function sendMessage(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Mark message as read
     *
     * @param int $messageId Message ID
     * @return array Updated message
     */
    public function markAsRead(int $messageId): array
    {
        return $this->update($messageId, ['read' => true]);
    }

    /**
     * Mark message as unread
     *
     * @param int $messageId Message ID
     * @return array Updated message
     */
    public function markAsUnread(int $messageId): array
    {
        return $this->update($messageId, ['read' => false]);
    }
}

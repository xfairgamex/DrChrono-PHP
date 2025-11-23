<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Prescription Messages Resource - Pharmacy communication management
 *
 * Manage electronic communication with pharmacies regarding prescriptions.
 * This includes refill requests, prescription change notifications, and
 * other pharmacy-to-provider messaging.
 *
 * API Endpoint: /api/prescription_messages
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#prescription_messages
 *
 * Use Cases:
 * - Electronic prescription refill requests
 * - Pharmacy-to-provider messaging
 * - Prescription status updates
 * - NCPDP SCRIPT messaging
 * - Medication therapy management communication
 */
class PrescriptionMessagesResource extends AbstractResource
{
    protected string $resourcePath = '/api/prescription_messages';

    /**
     * List prescription messages
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'prescription' (int): Filter by prescription ID
     *   - 'status' (string): Filter by message status
     *   - 'message_type' (string): Filter by message type
     *   - 'since' (string): Messages updated since timestamp
     * @return PagedCollection Collection of prescription messages
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific prescription message by ID
     *
     * @param int|string $messageId Message ID
     * @return array Prescription message details
     */
    public function get(int|string $messageId): array
    {
        return parent::get($messageId);
    }

    /**
     * Create a new prescription message
     *
     * Typical fields:
     * - patient (int): Patient ID (required)
     * - doctor (int): Doctor ID (required)
     * - prescription (int): Associated prescription ID
     * - message_type (string): Type of message
     * - content (string): Message content
     * - pharmacy_id (string): Pharmacy identifier
     *
     * @param array $data Message data
     * @return array Created message
     */
    public function createMessage(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update a prescription message
     *
     * @param int $messageId Message ID
     * @param array $data Updated data
     * @return array Updated message
     */
    public function updateMessage(int $messageId, array $data): array
    {
        return $this->update($messageId, $data);
    }

    /**
     * Delete a prescription message
     *
     * @param int $messageId Message ID
     * @return void
     */
    public function deleteMessage(int $messageId): void
    {
        $this->delete($messageId);
    }

    /**
     * Get prescription messages for a specific patient
     *
     * @param int $patientId Patient ID
     * @param array $filters Additional filters
     * @return PagedCollection Patient's prescription messages
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * Get prescription messages for a specific doctor
     *
     * @param int $doctorId Doctor ID
     * @param array $filters Additional filters
     * @return PagedCollection Doctor's prescription messages
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * Get messages for a specific prescription
     *
     * @param int $prescriptionId Prescription ID
     * @param array $filters Additional filters
     * @return PagedCollection Messages related to prescription
     */
    public function listByPrescription(int $prescriptionId, array $filters = []): PagedCollection
    {
        $filters['prescription'] = $prescriptionId;
        return $this->list($filters);
    }

    /**
     * Get messages by status
     *
     * Common statuses: 'pending', 'sent', 'delivered', 'failed', 'responded'
     *
     * @param string $status Message status
     * @param array $filters Additional filters
     * @return PagedCollection Messages with specified status
     */
    public function listByStatus(string $status, array $filters = []): PagedCollection
    {
        $filters['status'] = $status;
        return $this->list($filters);
    }

    /**
     * Get messages by type
     *
     * Common types: 'refill_request', 'new_rx', 'change_request', 'cancel_request'
     *
     * @param string $messageType Message type
     * @param array $filters Additional filters
     * @return PagedCollection Messages of specified type
     */
    public function listByType(string $messageType, array $filters = []): PagedCollection
    {
        $filters['message_type'] = $messageType;
        return $this->list($filters);
    }

    /**
     * Get pending refill requests
     *
     * Returns all pending prescription refill requests that need review.
     *
     * @param int|null $doctorId Optional doctor ID to filter by
     * @return PagedCollection Pending refill requests
     */
    public function getPendingRefillRequests(?int $doctorId = null): PagedCollection
    {
        $filters = [
            'message_type' => 'refill_request',
            'status' => 'pending',
        ];

        if ($doctorId !== null) {
            $filters['doctor'] = $doctorId;
        }

        return $this->list($filters);
    }

    /**
     * Get unread messages for a doctor
     *
     * @param int $doctorId Doctor ID
     * @return array List of unread messages
     */
    public function getUnreadByDoctor(int $doctorId): array
    {
        $messages = [];
        $collection = $this->listByDoctor($doctorId);

        foreach ($collection as $message) {
            if (!($message['is_read'] ?? false)) {
                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * Mark message as read
     *
     * @param int $messageId Message ID
     * @return array Updated message
     */
    public function markAsRead(int $messageId): array
    {
        return $this->updateMessage($messageId, ['is_read' => true]);
    }

    /**
     * Mark message as unread
     *
     * @param int $messageId Message ID
     * @return array Updated message
     */
    public function markAsUnread(int $messageId): array
    {
        return $this->updateMessage($messageId, ['is_read' => false]);
    }

    /**
     * Respond to a prescription message
     *
     * @param int $messageId Message ID
     * @param string $response Response content
     * @param array $additionalData Additional response data
     * @return array Updated message or response
     */
    public function respond(int $messageId, string $response, array $additionalData = []): array
    {
        $data = array_merge([
            'response' => $response,
            'status' => 'responded',
        ], $additionalData);

        return $this->updateMessage($messageId, $data);
    }

    /**
     * Get message history for a prescription
     *
     * Returns chronological message history for a specific prescription.
     *
     * @param int $prescriptionId Prescription ID
     * @return array Sorted message history
     */
    public function getMessageHistory(int $prescriptionId): array
    {
        $messages = [];
        $collection = $this->listByPrescription($prescriptionId);

        foreach ($collection as $message) {
            $messages[] = $message;
        }

        // Sort by created date if available
        usort($messages, function ($a, $b) {
            $dateA = $a['created_at'] ?? '';
            $dateB = $b['created_at'] ?? '';
            return strcmp($dateA, $dateB);
        });

        return $messages;
    }
}

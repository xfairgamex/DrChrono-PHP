<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Communication Logs Resource - Communication audit trail
 *
 * Track and audit all communication activities including phone calls, emails,
 * text messages, and other patient/provider interactions. Essential for
 * compliance, quality assurance, and care coordination documentation.
 *
 * API Endpoint: /api/comm_logs
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#comm_logs
 *
 * Use Cases:
 * - Communication audit trails
 * - HIPAA compliance documentation
 * - Care coordination tracking
 * - Patient contact history
 * - Quality assurance and monitoring
 * - Billing documentation (phone consultations)
 */
class CommLogsResource extends AbstractResource
{
    protected string $resourcePath = '/api/comm_logs';

    /**
     * List communication logs
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'user' (int): Filter by user ID
     *   - 'office' (int): Filter by office ID
     *   - 'communication_type' (string): Type of communication
     *   - 'since' (string): Logs created since timestamp
     *   - 'date' (string): Logs on specific date
     *   - 'date_range' (string): Date range filter
     * @return PagedCollection Collection of communication logs
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific communication log by ID
     *
     * @param int|string $logId Log ID
     * @return array Communication log details
     */
    public function get(int|string $logId): array
    {
        return parent::get($logId);
    }

    /**
     * Create a new communication log
     *
     * Typical fields:
     * - patient (int): Patient ID (required)
     * - doctor (int): Doctor ID
     * - user (int): User who made the communication
     * - office (int): Office ID
     * - communication_type (string): Type (e.g., 'phone', 'email', 'text', 'in_person')
     * - direction (string): 'inbound' or 'outbound'
     * - subject (string): Subject/title of communication
     * - notes (string): Communication notes/details
     * - duration_minutes (int): Duration for phone calls
     * - date (string): Date of communication
     *
     * @param array $data Log data
     * @return array Created communication log
     */
    public function createLog(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update a communication log
     *
     * @param int $logId Log ID
     * @param array $data Updated data
     * @return array Updated log
     */
    public function updateLog(int $logId, array $data): array
    {
        return $this->update($logId, $data);
    }

    /**
     * Delete a communication log
     *
     * @param int $logId Log ID
     * @return void
     */
    public function deleteLog(int $logId): void
    {
        $this->delete($logId);
    }

    /**
     * Get communication logs for a specific patient
     *
     * @param int $patientId Patient ID
     * @param array $filters Additional filters
     * @return PagedCollection Patient's communication logs
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * Get communication logs for a specific doctor
     *
     * @param int $doctorId Doctor ID
     * @param array $filters Additional filters
     * @return PagedCollection Doctor's communication logs
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * Get communication logs for a specific user
     *
     * @param int $userId User ID
     * @param array $filters Additional filters
     * @return PagedCollection User's communication logs
     */
    public function listByUser(int $userId, array $filters = []): PagedCollection
    {
        $filters['user'] = $userId;
        return $this->list($filters);
    }

    /**
     * Get communication logs by type
     *
     * Common types: 'phone', 'email', 'text', 'in_person', 'video', 'fax'
     *
     * @param string $communicationType Communication type
     * @param array $filters Additional filters
     * @return PagedCollection Logs of specified type
     */
    public function listByType(string $communicationType, array $filters = []): PagedCollection
    {
        $filters['communication_type'] = $communicationType;
        return $this->list($filters);
    }

    /**
     * Get communication logs by date range
     *
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @param array $filters Additional filters
     * @return PagedCollection Logs within date range
     */
    public function listByDateRange(string $startDate, string $endDate, array $filters = []): PagedCollection
    {
        $filters['date_range'] = $startDate . '/' . $endDate;
        return $this->list($filters);
    }

    /**
     * Get phone call logs
     *
     * Returns all phone call communication logs.
     *
     * @param array $filters Additional filters
     * @return PagedCollection Phone call logs
     */
    public function listPhoneCalls(array $filters = []): PagedCollection
    {
        return $this->listByType('phone', $filters);
    }

    /**
     * Get email logs
     *
     * Returns all email communication logs.
     *
     * @param array $filters Additional filters
     * @return PagedCollection Email logs
     */
    public function listEmails(array $filters = []): PagedCollection
    {
        return $this->listByType('email', $filters);
    }

    /**
     * Get text message logs
     *
     * Returns all text/SMS communication logs.
     *
     * @param array $filters Additional filters
     * @return PagedCollection Text message logs
     */
    public function listTextMessages(array $filters = []): PagedCollection
    {
        return $this->listByType('text', $filters);
    }

    /**
     * Get inbound communication logs
     *
     * Returns all inbound communications (patient-to-practice).
     *
     * @param array $filters Additional filters
     * @return array Inbound communication logs
     */
    public function listInbound(array $filters = []): array
    {
        $logs = [];
        $collection = $this->list($filters);

        foreach ($collection as $log) {
            if (($log['direction'] ?? '') === 'inbound') {
                $logs[] = $log;
            }
        }

        return $logs;
    }

    /**
     * Get outbound communication logs
     *
     * Returns all outbound communications (practice-to-patient).
     *
     * @param array $filters Additional filters
     * @return array Outbound communication logs
     */
    public function listOutbound(array $filters = []): array
    {
        $logs = [];
        $collection = $this->list($filters);

        foreach ($collection as $log) {
            if (($log['direction'] ?? '') === 'outbound') {
                $logs[] = $log;
            }
        }

        return $logs;
    }

    /**
     * Log a phone call
     *
     * Convenience method to create a phone call log.
     *
     * @param int $patientId Patient ID
     * @param string $direction 'inbound' or 'outbound'
     * @param int $durationMinutes Call duration in minutes
     * @param string $notes Call notes
     * @param array $additionalData Additional data
     * @return array Created log
     */
    public function logPhoneCall(
        int $patientId,
        string $direction,
        int $durationMinutes,
        string $notes,
        array $additionalData = []
    ): array {
        $data = array_merge([
            'patient' => $patientId,
            'communication_type' => 'phone',
            'direction' => $direction,
            'duration_minutes' => $durationMinutes,
            'notes' => $notes,
        ], $additionalData);

        return $this->createLog($data);
    }

    /**
     * Log an email
     *
     * Convenience method to create an email log.
     *
     * @param int $patientId Patient ID
     * @param string $direction 'inbound' or 'outbound'
     * @param string $subject Email subject
     * @param string $notes Email notes/content
     * @param array $additionalData Additional data
     * @return array Created log
     */
    public function logEmail(
        int $patientId,
        string $direction,
        string $subject,
        string $notes,
        array $additionalData = []
    ): array {
        $data = array_merge([
            'patient' => $patientId,
            'communication_type' => 'email',
            'direction' => $direction,
            'subject' => $subject,
            'notes' => $notes,
        ], $additionalData);

        return $this->createLog($data);
    }

    /**
     * Get patient's complete communication history
     *
     * Returns all communication logs for a patient, sorted by date.
     *
     * @param int $patientId Patient ID
     * @return array Sorted communication history
     */
    public function getPatientHistory(int $patientId): array
    {
        $logs = [];
        $collection = $this->listByPatient($patientId);

        foreach ($collection as $log) {
            $logs[] = $log;
        }

        // Sort by date/created_at (newest first)
        usort($logs, function ($a, $b) {
            $dateA = $a['date'] ?? $a['created_at'] ?? '';
            $dateB = $b['date'] ?? $b['created_at'] ?? '';
            return strcmp($dateB, $dateA); // Descending order
        });

        return $logs;
    }

    /**
     * Get recent communication logs
     *
     * Returns the most recent communication logs across all patients.
     *
     * @param int $limit Number of logs to return
     * @param array $filters Additional filters
     * @return array Recent logs
     */
    public function getRecent(int $limit = 50, array $filters = []): array
    {
        $logs = [];
        $collection = $this->list($filters);
        $count = 0;

        foreach ($collection as $log) {
            $logs[] = $log;
            $count++;

            if ($count >= $limit) {
                break;
            }
        }

        return $logs;
    }
}

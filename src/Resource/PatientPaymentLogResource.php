<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patient Payment Log - Track payment history and audit trail
 *
 * The patient payment log provides a comprehensive audit trail of all payment
 * activities, including who made changes, when, and what was modified.
 *
 * API Endpoint: /api/patient_payment_log
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#patient_payment_log
 */
class PatientPaymentLogResource extends AbstractResource
{
    protected string $resourcePath = '/api/patient_payment_log';

    /**
     * List payment log entries
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'payment' (int): Filter by payment ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Filter by creation date
     *   - 'action' (string): Filter by action type
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific log entry
     *
     * @param int|string $logId Log entry ID
     * @return array Log entry data
     */
    public function get(int|string $logId): array
    {
        return parent::get($logId);
    }

    /**
     * List log entries for a specific patient
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
     * List log entries for a specific payment
     *
     * @param int $paymentId Payment ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByPayment(int $paymentId, array $filters = []): PagedCollection
    {
        $filters['payment'] = $paymentId;
        return $this->list($filters);
    }

    /**
     * List log entries for a specific doctor
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
     * Get payment history for a patient
     *
     * Retrieves all payment-related activities for a patient,
     * useful for audit trails and reconciliation
     *
     * @param int $patientId Patient ID
     * @param array $filters Additional filters (date range, action type)
     * @return PagedCollection
     */
    public function getPaymentHistory(int $patientId, array $filters = []): PagedCollection
    {
        return $this->listByPatient($patientId, $filters);
    }

    /**
     * Get recent payment activity
     *
     * @param int $days Number of days to look back
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function getRecentActivity(int $days = 30, array $filters = []): PagedCollection
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));
        $filters['since'] = $since;
        return $this->list($filters);
    }
}

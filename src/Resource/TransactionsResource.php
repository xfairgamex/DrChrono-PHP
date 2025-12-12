<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Transactions - Manage payment transactions
 *
 * Transactions represent payments, adjustments, and other financial activities
 * related to patient accounts and insurance claims.
 *
 * API Endpoint: /api/transactions
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#transactions
 */
class TransactionsResource extends AbstractResource
{
    protected string $resourcePath = '/api/transactions';

    /**
     * List transactions
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'appointment' (int): Filter by appointment ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Filter by update date
     *   - 'transaction_type' (string): Type of transaction
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific transaction
     *
     * @param int|string $transactionId Transaction ID
     * @return array Transaction data
     */
    public function get(int|string $transactionId): array
    {
        return parent::get($transactionId);
    }

    /**
     * List transactions for a specific patient
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
     * List transactions for a specific appointment
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
     * List transactions for a specific doctor
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
     * Create a new transaction
     *
     * Required fields:
     * - appointment (int): Appointment ID
     * - amount (float): Transaction amount
     * - transaction_type (string): Type (Payment, Adjustment, etc.)
     *
     * Optional fields:
     * - posted_date (string): Date posted (YYYY-MM-DD)
     * - check_number (string): Check number for check payments
     * - ins_name (string): Insurance company name
     * - note (string): Transaction notes
     *
     * @param array $data Transaction data
     * @return array Created transaction
     */
    public function createTransaction(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing transaction
     *
     * @param int $transactionId Transaction ID
     * @param array $data Updated data
     * @return array Updated transaction
     */
    public function updateTransaction(int $transactionId, array $data): array
    {
        return $this->update($transactionId, $data);
    }

    /**
     * Delete a transaction
     *
     * @param int $transactionId Transaction ID
     * @return void
     */
    public function deleteTransaction(int $transactionId): void
    {
        $this->delete($transactionId);
    }

    /**
     * Record a payment transaction
     *
     * Convenience method to create a payment transaction
     *
     * @param int $appointmentId Appointment ID
     * @param float $amount Payment amount
     * @param array $additionalData Additional transaction data
     * @return array Created payment transaction
     */
    public function recordPayment(int $appointmentId, float $amount, array $additionalData = []): array
    {
        $data = array_merge([
            'appointment' => $appointmentId,
            'amount' => $amount,
            'transaction_type' => 'Payment',
        ], $additionalData);

        return $this->createTransaction($data);
    }

    /**
     * Record an adjustment transaction
     *
     * Convenience method to create an adjustment transaction
     *
     * @param int $appointmentId Appointment ID
     * @param float $amount Adjustment amount
     * @param array $additionalData Additional transaction data
     * @return array Created adjustment transaction
     */
    public function recordAdjustment(int $appointmentId, float $amount, array $additionalData = []): array
    {
        $data = array_merge([
            'appointment' => $appointmentId,
            'amount' => $amount,
            'transaction_type' => 'Adjustment',
        ], $additionalData);

        return $this->createTransaction($data);
    }
}

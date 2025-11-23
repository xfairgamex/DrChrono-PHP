<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patient Payments - Manage patient payment records
 *
 * Track payments made by patients including copays, deductibles, and
 * out-of-pocket expenses.
 *
 * API Endpoint: /api/patient_payments
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#patient_payments
 */
class PatientPaymentsResource extends AbstractResource
{
    protected string $resourcePath = '/api/patient_payments';

    /**
     * List patient payments
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'appointment' (int): Filter by appointment ID
     *   - 'since' (string): Updated since timestamp
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get payments for a specific patient
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
     * Get payments for a specific appointment
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
     * Create patient payment
     *
     * Required fields:
     * - patient (int): Patient ID
     * - amount (float): Payment amount
     * - payment_method (string): Payment method (e.g., 'Cash', 'Check', 'Credit Card')
     *
     * @param array $data Payment data
     * @return array Created payment record
     */
    public function createPayment(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update patient payment
     *
     * @param int $paymentId Payment ID
     * @param array $data Updated data
     * @return array Updated payment record
     */
    public function updatePayment(int $paymentId, array $data): array
    {
        return $this->update($paymentId, $data);
    }

    /**
     * Delete patient payment
     *
     * @param int $paymentId Payment ID
     * @return void
     */
    public function deletePayment(int $paymentId): void
    {
        $this->delete($paymentId);
    }
}

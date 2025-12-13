<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Appointments resource - manage appointments and scheduling
 *
 * This resource provides methods for managing appointments, including creating,
 * updating, listing, and retrieving appointment data with various filters.
 *
 * @see https://app.drchrono.com/api-docs/#tag/Appointments Official API documentation
 */
class AppointmentsResource extends AbstractResource
{
    protected string $resourcePath = '/api/appointments';

    /**
     * List appointments within a specific date range
     *
     * @param string $startDate Start date (YYYY-MM-DD format)
     * @param string $endDate End date (YYYY-MM-DD format)
     * @param array $filters Optional additional filters (status, doctor, office, etc.)
     * @return PagedCollection Paginated appointment results
     *
     * @example
     * // Get all appointments for January 2024
     * $appointments = $client->appointments->listByDateRange('2024-01-01', '2024-01-31');
     *
     * // Get completed appointments for specific doctor
     * $appointments = $client->appointments->listByDateRange(
     *     '2024-01-01',
     *     '2024-01-31',
     *     ['doctor' => 123, 'status' => 'Complete']
     * );
     */
    public function listByDateRange(string $startDate, string $endDate, array $filters = []): PagedCollection
    {
        $filters['date_range'] = "{$startDate}/{$endDate}";
        return $this->list($filters);
    }

    /**
     * List appointments for a specific doctor
     *
     * @param int $doctorId Doctor ID
     * @param array $filters Optional additional filters (date, status, office, etc.)
     * @return PagedCollection Paginated appointment results
     *
     * @example
     * // Get all appointments for a doctor
     * $appointments = $client->appointments->listByDoctor(123);
     *
     * // Get today's appointments for a doctor
     * $appointments = $client->appointments->listByDoctor(123, [
     *     'date' => date('Y-m-d'),
     * ]);
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * List appointments for specific patient
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * List appointments for specific office
     */
    public function listByOffice(int $officeId, array $filters = []): PagedCollection
    {
        $filters['office'] = $officeId;
        return $this->list($filters);
    }

    /**
     * Create a new appointment
     *
     * @param array $appointmentData Appointment data
     * @return array Created appointment data
     *
     * Required fields:
     * - doctor (int): Doctor ID
     * - duration (int): Duration in minutes
     * - office (int): Office ID
     * - patient (int): Patient ID
     * - scheduled_time (string): ISO 8601 datetime (e.g., '2024-01-15T10:00:00')
     *
     * @example
     * $appointment = $client->appointments->createAppointment([
     *     'doctor' => 123,
     *     'patient' => 456,
     *     'office' => 1,
     *     'duration' => 30,
     *     'scheduled_time' => '2024-01-15T10:00:00',
     *     'exam_room' => 2,
     *     'status' => 'Scheduled',
     *     'reason' => 'Annual checkup',
     * ]);
     *
     * echo "Created appointment ID: {$appointment['id']}\n";
     */
    public function createAppointment(array $appointmentData): array
    {
        return $this->create($appointmentData);
    }

    /**
     * Update appointment
     */
    public function updateAppointment(int $appointmentId, array $appointmentData): array
    {
        return $this->update($appointmentId, $appointmentData);
    }

    /**
     * Set appointment status
     */
    public function setStatus(int $appointmentId, string $status): array
    {
        return $this->update($appointmentId, ['status' => $status]);
    }

    /**
     * Cancel appointment
     */
    public function cancel(int $appointmentId, string $reason = ''): array
    {
        $data = ['status' => 'Cancelled'];
        if ($reason) {
            $data['notes'] = $reason;
        }
        return $this->update($appointmentId, $data);
    }

    /**
     * Mark appointment as arrived
     */
    public function markArrived(int $appointmentId): array
    {
        return $this->setStatus($appointmentId, 'Arrived');
    }

    /**
     * Mark appointment as complete
     */
    public function markComplete(int $appointmentId): array
    {
        return $this->setStatus($appointmentId, 'Complete');
    }

    /**
     * Get appointment with clinical details (verbose mode)
     *
     * Uses verbose mode to include additional fields that require extra database queries:
     * - clinical_note: Full clinical note object (if exists)
     * - vitals: Standard vital signs (BP, temp, pulse, weight, height, etc.)
     * - custom_vitals: Custom vital measurements
     * - status_transitions: Complete appointment status change history
     * - reminders: Configured appointment reminders
     * - extended_updated_at: Extended timestamp information
     *
     * @param int $appointmentId Appointment ID
     * @return array Appointment data with clinical details
     *
     * @example
     * $appointment = $client->appointments->getWithClinicalData(12345);
     *
     * // Access clinical note
     * if (isset($appointment['clinical_note'])) {
     *     echo "Clinical Note ID: {$appointment['clinical_note']['id']}\n";
     * }
     *
     * // Access vitals
     * if (isset($appointment['vitals'])) {
     *     $bp1 = $appointment['vitals']['blood_pressure_1'];
     *     $bp2 = $appointment['vitals']['blood_pressure_2'];
     *     echo "Blood Pressure: {$bp1}/{$bp2}\n";
     *     echo "Temperature: {$appointment['vitals']['temperature']}°F\n";
     * }
     *
     * // View status history
     * foreach ($appointment['status_transitions'] ?? [] as $transition) {
     *     echo "Status: {$transition['status']} at {$transition['created_at']}\n";
     * }
     *
     * @see https://github.com/drchrono/php-sdk/blob/main/docs/VERBOSE_MODE.md Verbose Mode Guide
     */
    public function getWithClinicalData(int $appointmentId): array
    {
        return $this->getVerbose($appointmentId);
    }

    /**
     * List appointments with clinical data (verbose mode)
     *
     * Returns appointments with additional clinical fields. See getWithClinicalData()
     * for the list of included verbose fields.
     *
     * Performance Note: Page size is reduced from 250 to 50 records in verbose mode.
     * Each record requires additional database queries.
     *
     * @param array $filters Optional filters (date, doctor, patient, office, status)
     * @return PagedCollection Paginated results with verbose appointment data
     *
     * @example
     * // Get today's appointments with clinical data
     * $appointments = $client->appointments->listWithClinicalData([
     *     'date' => date('Y-m-d'),
     *     'status' => 'Complete',
     * ]);
     *
     * foreach ($appointments as $appt) {
     *     echo "Appointment {$appt['id']}: {$appt['patient']['first_name']}\n";
     *
     *     if (isset($appt['vitals'])) {
     *         echo "  Vitals recorded: Yes\n";
     *     }
     *
     *     if (isset($appt['clinical_note'])) {
     *         echo "  Note ID: {$appt['clinical_note']['id']}\n";
     *     }
     * }
     *
     * @see https://github.com/drchrono/php-sdk/blob/main/docs/VERBOSE_MODE.md Verbose Mode Guide
     */
    public function listWithClinicalData(array $filters = []): PagedCollection
    {
        return $this->listVerbose($filters);
    }
}

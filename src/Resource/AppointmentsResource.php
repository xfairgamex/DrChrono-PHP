<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Appointments resource - manage appointments and scheduling
 */
class AppointmentsResource extends AbstractResource
{
    protected string $resourcePath = '/api/appointments';

    /**
     * List appointments with filters (date, status, patient, doctor, office)
     */
    public function listByDateRange(string $startDate, string $endDate, array $filters = []): PagedCollection
    {
        $filters['date_range'] = "{$startDate}/{$endDate}";
        return $this->list($filters);
    }

    /**
     * List appointments for specific doctor
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
     * Create appointment
     * Required fields: doctor, duration, office, patient, scheduled_time
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
}

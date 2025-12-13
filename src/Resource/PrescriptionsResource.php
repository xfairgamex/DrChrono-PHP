<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Prescriptions resource - manage medication prescriptions
 *
 * This resource handles creating, updating, and tracking medication prescriptions
 * including e-prescribing (eRx) when integrated with pharmacy systems.
 *
 * @see https://app.drchrono.com/api-docs/#tag/Prescriptions Official API documentation
 */
class PrescriptionsResource extends AbstractResource
{
    protected string $resourcePath = '/api/prescriptions';

    /**
     * List prescriptions for a specific patient
     *
     * @param int $patientId Patient ID
     * @param array $filters Optional additional filters (doctor, since, status)
     * @return PagedCollection Paginated prescription results
     *
     * @example
     * // Get all prescriptions for a patient
     * $prescriptions = $client->prescriptions->listByPatient(12345);
     *
     * // Get active prescriptions only
     * $prescriptions = $client->prescriptions->listByPatient(12345, [
     *     'status' => 'active',
     * ]);
     *
     * foreach ($prescriptions as $rx) {
     *     echo "{$rx['medication']}: {$rx['dosage']}\n";
     *     echo "Refills: {$rx['refills']}\n";
     * }
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * List prescriptions by doctor
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * Create a new prescription
     *
     * @param array $prescriptionData Prescription data
     * @return array Created prescription data
     *
     * Required fields:
     * - doctor (int): Prescribing doctor ID
     * - patient (int): Patient ID
     * - medication (string): Medication name
     * - dosage (string): Dosage instructions
     *
     * @example
     * // Create a simple prescription
     * $prescription = $client->prescriptions->createPrescription([
     *     'doctor' => 123,
     *     'patient' => 456,
     *     'medication' => 'Lisinopril',
     *     'dosage' => '10mg once daily',
     *     'quantity' => '30',
     *     'refills' => '3',
     *     'pharmacy_note' => 'Generic substitution allowed',
     * ]);
     *
     * echo "Created prescription ID: {$prescription['id']}\n";
     *
     * // Create prescription with detailed instructions
     * $prescription = $client->prescriptions->createPrescription([
     *     'doctor' => 123,
     *     'patient' => 456,
     *     'medication' => 'Amoxicillin 500mg',
     *     'dosage' => 'Take 1 capsule by mouth three times daily',
     *     'quantity' => '21',
     *     'refills' => '0',
     *     'notes' => 'Complete full course even if symptoms improve',
     *     'daw' => false,  // Dispense as written
     * ]);
     */
    public function createPrescription(array $prescriptionData): array
    {
        return $this->create($prescriptionData);
    }

    /**
     * Update prescription
     */
    public function updatePrescription(int $prescriptionId, array $prescriptionData): array
    {
        return $this->update($prescriptionId, $prescriptionData);
    }

    /**
     * Get prescription details
     */
    public function getPrescription(int $prescriptionId): array
    {
        return $this->get($prescriptionId);
    }
}

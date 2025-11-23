<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Medications resource - manage patient medications
 */
class MedicationsResource extends AbstractResource
{
    protected string $resourcePath = '/api/medications';

    /**
     * List medications for patient
     */
    public function listByPatient(int $patientId): PagedCollection
    {
        return $this->list(['patient' => $patientId]);
    }

    /**
     * Create medication
     */
    public function createMedication(array $medicationData): array
    {
        return $this->create($medicationData);
    }

    /**
     * Update medication
     */
    public function updateMedication(int $medicationId, array $medicationData): array
    {
        return $this->update($medicationId, $medicationData);
    }

    /**
     * Delete medication
     */
    public function deleteMedication(int $medicationId): void
    {
        $this->delete($medicationId);
    }

    /**
     * Append note to pharmacy note field
     *
     * Appends text to the pharmacy note field of a medication.
     * This is useful for adding additional instructions or notes
     * without overwriting existing content.
     *
     * @param int $medicationId Medication ID
     * @param string $note Note text to append
     * @return array Updated medication
     */
    public function appendToPharmacyNote(int $medicationId, string $note): array
    {
        return $this->httpClient->patch(
            "{$this->resourcePath}/{$medicationId}/append_to_pharmacy_note",
            ['note' => $note]
        );
    }
}

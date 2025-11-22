<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Clinical Notes resource - manage clinical documentation
 */
class ClinicalNotesResource extends AbstractResource
{
    protected string $resourcePath = '/api/clinical_notes';

    /**
     * List clinical notes with filters
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * List clinical notes by appointment
     */
    public function listByAppointment(int $appointmentId): PagedCollection
    {
        return $this->list(['appointment' => $appointmentId]);
    }

    /**
     * List clinical notes by doctor
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * Create clinical note
     */
    public function createNote(array $noteData): array
    {
        return $this->create($noteData);
    }

    /**
     * Update clinical note
     */
    public function updateNote(int $noteId, array $noteData): array
    {
        return $this->update($noteId, $noteData);
    }

    /**
     * Lock clinical note (finalize)
     */
    public function lock(int $noteId): array
    {
        return $this->update($noteId, ['is_locked' => true]);
    }

    /**
     * Get clinical note templates
     */
    public function getTemplates(): array
    {
        return $this->httpClient->get('/api/clinical_note_templates');
    }

    /**
     * Get clinical note field values
     */
    public function getFieldValues(int $noteId): array
    {
        return $this->httpClient->get("/api/clinical_note_field_values", ['clinical_note' => $noteId]);
    }

    /**
     * Update clinical note field value
     */
    public function updateFieldValue(int $fieldValueId, $value): array
    {
        return $this->httpClient->patch("/api/clinical_note_field_values/{$fieldValueId}", ['value' => $value]);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Clinical Notes resource - manage clinical documentation
 *
 * This resource provides methods for creating, updating, and retrieving clinical notes,
 * including support for verbose mode to retrieve complete section content.
 *
 * @see https://app.drchrono.com/api-docs/#tag/Clinical-Notes Official API documentation
 */
class ClinicalNotesResource extends AbstractResource
{
    protected string $resourcePath = '/api/clinical_notes';

    /**
     * List clinical notes for a specific patient
     *
     * @param int $patientId Patient ID
     * @param array $filters Optional additional filters (since, appointment, doctor)
     * @return PagedCollection Paginated clinical note results
     *
     * @example
     * // Get all notes for a patient
     * $notes = $client->clinicalNotes->listByPatient(12345);
     *
     * // Get recent notes (since specific date)
     * $notes = $client->clinicalNotes->listByPatient(12345, [
     *     'since' => '2024-01-01',
     * ]);
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
     * Create a new clinical note
     *
     * @param array $noteData Clinical note data
     * @return array Created clinical note data
     *
     * Required fields:
     * - appointment (int): Appointment ID
     * - doctor (int): Doctor ID
     * - patient (int): Patient ID
     *
     * @example
     * $note = $client->clinicalNotes->createNote([
     *     'appointment' => 98765,
     *     'doctor' => 123,
     *     'patient' => 456,
     *     'clinical_note_template' => 789,
     * ]);
     *
     * echo "Created note ID: {$note['id']}\n";
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
     * Lock a clinical note (finalize/sign)
     *
     * Once locked, a clinical note cannot be edited. This is typically done
     * when the provider signs off on the documentation.
     *
     * @param int $noteId Clinical note ID
     * @return array Updated clinical note data
     *
     * @example
     * $note = $client->clinicalNotes->lock(12345);
     * echo "Note locked: " . ($note['is_locked'] ? 'Yes' : 'No') . "\n";
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

    /**
     * Get clinical note with detailed sections (verbose mode)
     *
     * Uses verbose mode to include additional fields that require extra database queries:
     * - clinical_note_sections: Complete note sections with all field values and content
     *
     * @param int $noteId Clinical note ID
     * @return array Clinical note data with detailed sections
     *
     * @example
     * $note = $client->clinicalNotes->getWithSections(12345);
     *
     * // Iterate through sections
     * if (isset($note['clinical_note_sections'])) {
     *     foreach ($note['clinical_note_sections'] as $section) {
     *         echo "\n{$section['section_name']}:\n";
     *         echo "{$section['section_content']}\n";
     *
     *         // Access individual fields within section
     *         if (isset($section['fields'])) {
     *             foreach ($section['fields'] as $field) {
     *                 echo "  {$field['field_name']}: {$field['field_value']}\n";
     *             }
     *         }
     *     }
     * }
     *
     * @see https://github.com/drchrono/php-sdk/blob/main/docs/VERBOSE_MODE.md Verbose Mode Guide
     */
    public function getWithSections(int $noteId): array
    {
        return $this->getVerbose($noteId);
    }

    /**
     * List clinical notes with detailed sections (verbose mode)
     *
     * Returns clinical notes with complete section content. See getWithSections()
     * for the list of included verbose fields.
     *
     * Performance Note: Page size is reduced from 250 to 50 records in verbose mode.
     * Each record requires additional database queries.
     *
     * @param array $filters Optional filters (patient, doctor, appointment, since)
     * @return PagedCollection Paginated results with verbose clinical note data
     *
     * @example
     * // Get all notes for a patient with full content
     * $notes = $client->clinicalNotes->listWithSections([
     *     'patient' => 12345,
     *     'since' => '2024-01-01',
     * ]);
     *
     * foreach ($notes as $note) {
     *     echo "Note {$note['id']} - {$note['created_at']}\n";
     *
     *     if (isset($note['clinical_note_sections'])) {
     *         echo "  Sections: " . count($note['clinical_note_sections']) . "\n";
     *
     *         // Find specific section (e.g., Chief Complaint)
     *         foreach ($note['clinical_note_sections'] as $section) {
     *             if ($section['section_name'] === 'Chief Complaint') {
     *                 echo "  Chief Complaint: {$section['section_content']}\n";
     *                 break;
     *             }
     *         }
     *     }
     * }
     *
     * @see https://github.com/drchrono/php-sdk/blob/main/docs/VERBOSE_MODE.md Verbose Mode Guide
     */
    public function listWithSections(array $filters = []): PagedCollection
    {
        return $this->listVerbose($filters);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patient Physical Exams - Record physical examination findings
 *
 * Physical exams document clinical observations and findings from
 * systematic patient examinations.
 *
 * API Endpoint: /api/patient_physical_exams
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#patient_physical_exams
 */
class PatientPhysicalExamsResource extends AbstractResource
{
    protected string $resourcePath = '/api/patient_physical_exams';

    /**
     * List physical exams
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'appointment' (int): Filter by appointment ID
     *   - 'since' (string): Filter by update date
     *   - 'date' (string): Filter by exam date
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific physical exam
     *
     * @param int|string $examId Exam ID
     * @return array Exam data
     */
    public function get(int|string $examId): array
    {
        return parent::get($examId);
    }

    /**
     * List physical exams for a specific patient
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
     * List physical exams by doctor
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
     * List physical exams by appointment
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
     * Create a new physical exam record
     *
     * Required fields:
     * - patient (int): Patient ID
     * - date (string): Exam date (YYYY-MM-DD)
     *
     * Optional fields:
     * - doctor (int): Examining doctor ID
     * - appointment (int): Associated appointment ID
     * - general_appearance (string): General appearance findings
     * - head_eyes_ears_nose_throat (string): HEENT findings
     * - cardiovascular (string): Cardiovascular findings
     * - respiratory (string): Respiratory findings
     * - gastrointestinal (string): GI findings
     * - musculoskeletal (string): Musculoskeletal findings
     * - neurological (string): Neurological findings
     * - skin (string): Skin findings
     * - notes (string): Additional notes
     *
     * @param array $data Exam data
     * @return array Created exam
     */
    public function createExam(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing physical exam
     *
     * @param int $examId Exam ID
     * @param array $data Updated data
     * @return array Updated exam
     */
    public function updateExam(int $examId, array $data): array
    {
        return $this->update($examId, $data);
    }

    /**
     * Delete a physical exam
     *
     * @param int $examId Exam ID
     * @return void
     */
    public function deleteExam(int $examId): void
    {
        $this->delete($examId);
    }

    /**
     * Get the most recent exam for a patient
     *
     * @param int $patientId Patient ID
     * @return array|null Most recent exam or null
     */
    public function getMostRecent(int $patientId): ?array
    {
        $exams = $this->listByPatient($patientId);
        $items = iterator_to_array($exams);

        if (empty($items)) {
            return null;
        }

        // Sort by date descending and return first
        usort($items, function ($a, $b) {
            return strtotime($b['date'] ?? '0') - strtotime($a['date'] ?? '0');
        });

        return $items[0];
    }

    /**
     * Get exams within a date range
     *
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByDateRange(string $startDate, string $endDate, array $filters = []): PagedCollection
    {
        $filters['date_range'] = "{$startDate}/{$endDate}";
        return $this->list($filters);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Patient Vaccine Records - Track patient immunization history
 *
 * Patient vaccine records document immunization history for clinical
 * reference, reporting, and compliance tracking.
 *
 * API Endpoint: /api/patient_vaccine_records
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#patient_vaccine_records
 */
class PatientVaccineRecordsResource extends AbstractResource
{
    protected string $resourcePath = '/api/patient_vaccine_records';

    /**
     * List patient vaccine records
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'vaccine' (int): Filter by vaccine ID
     *   - 'since' (string): Filter by update date
     *   - 'administered_at' (string): Filter by administration date
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific vaccine record
     *
     * @param int|string $recordId Record ID
     * @return array Record data
     */
    public function get(int|string $recordId): array
    {
        return parent::get($recordId);
    }

    /**
     * List vaccine records for a specific patient
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
     * List vaccine records by doctor
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
     * List vaccine records by vaccine type
     *
     * @param int $vaccineId Vaccine ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByVaccine(int $vaccineId, array $filters = []): PagedCollection
    {
        $filters['vaccine'] = $vaccineId;
        return $this->list($filters);
    }

    /**
     * Create a new vaccine record
     *
     * Required fields:
     * - patient (int): Patient ID
     * - vaccine (int): Vaccine ID
     * - administered_at (string): Administration date (YYYY-MM-DD)
     *
     * Optional fields:
     * - doctor (int): Administering doctor ID
     * - dose (float): Dose amount
     * - units (string): Dose units
     * - route (string): Administration route
     * - site (string): Administration site
     * - lot_number (string): Vaccine lot number
     * - manufacturer (string): Vaccine manufacturer
     * - expiration_date (string): Vaccine expiration date
     * - notes (string): Additional notes
     * - vis_date (string): VIS publication date
     * - ordering_doctor (int): Ordering doctor ID
     *
     * @param array $data Record data
     * @return array Created record
     */
    public function createRecord(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing vaccine record
     *
     * @param int $recordId Record ID
     * @param array $data Updated data
     * @return array Updated record
     */
    public function updateRecord(int $recordId, array $data): array
    {
        return $this->update($recordId, $data);
    }

    /**
     * Delete a vaccine record
     *
     * @param int $recordId Record ID
     * @return void
     */
    public function deleteRecord(int $recordId): void
    {
        $this->delete($recordId);
    }

    /**
     * Get vaccine records within a date range
     *
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByDateRange(string $startDate, string $endDate, array $filters = []): PagedCollection
    {
        $filters['administered_at__range'] = "{$startDate},{$endDate}";
        return $this->list($filters);
    }

    /**
     * Get patient's immunization history
     *
     * Returns all vaccine records for a patient ordered by date
     *
     * @param int $patientId Patient ID
     * @return PagedCollection
     */
    public function getImmunizationHistory(int $patientId): PagedCollection
    {
        return $this->list([
            'patient' => $patientId,
            'ordering' => '-administered_at'
        ]);
    }

    /**
     * Get records by lot number (for recall tracking)
     *
     * @param string $lotNumber Lot number
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function getByLotNumber(string $lotNumber, array $filters = []): PagedCollection
    {
        $filters['lot_number'] = $lotNumber;
        return $this->list($filters);
    }
}

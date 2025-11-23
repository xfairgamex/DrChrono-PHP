<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Line Items - Manage invoice line items and billing codes
 *
 * Line items represent billable procedures, services, and codes associated with
 * appointments. They are the individual items that appear on claims and invoices.
 *
 * API Endpoint: /api/line_items
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#line_items
 */
class LineItemsResource extends AbstractResource
{
    protected string $resourcePath = '/api/line_items';

    /**
     * List line items
     *
     * @param array $filters Optional filters:
     *   - 'appointment' (int): Filter by appointment ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'patient' (int): Filter by patient ID
     *   - 'since' (string): Filter by update date
     *   - 'code' (string): Filter by procedure code
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific line item
     *
     * @param int|string $lineItemId Line item ID
     * @return array Line item data
     */
    public function get(int|string $lineItemId): array
    {
        return parent::get($lineItemId);
    }

    /**
     * List line items for a specific appointment
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
     * List line items for a specific doctor
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
     * List line items for a specific patient
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
     * Create a new line item
     *
     * Required fields:
     * - appointment (int): Appointment ID
     * - code (string): Procedure/service code (CPT, HCPCS, etc.)
     * - procedure_type (string): Type of procedure code
     *
     * Optional fields:
     * - doctor (int): Rendering provider ID
     * - quantity (int): Quantity/units
     * - price (float): Unit price
     * - adjustment (float): Adjustment amount
     * - modifiers (array): Code modifiers
     * - diagnosis_pointers (array): ICD-10 diagnosis pointers
     * - units (string): Unit type
     * - place_of_service (int): Place of service code
     *
     * @param array $data Line item data
     * @return array Created line item
     */
    public function createLineItem(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing line item
     *
     * @param int $lineItemId Line item ID
     * @param array $data Updated data
     * @return array Updated line item
     */
    public function updateLineItem(int $lineItemId, array $data): array
    {
        return $this->update($lineItemId, $data);
    }

    /**
     * Delete a line item
     *
     * @param int $lineItemId Line item ID
     * @return void
     */
    public function deleteLineItem(int $lineItemId): void
    {
        $this->delete($lineItemId);
    }

    /**
     * Get line items by procedure code
     *
     * @param string $code Procedure code
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByCode(string $code, array $filters = []): PagedCollection
    {
        $filters['code'] = $code;
        return $this->list($filters);
    }

    /**
     * Add procedure to appointment
     *
     * Convenience method to quickly add a billable procedure to an appointment
     *
     * @param int $appointmentId Appointment ID
     * @param string $code Procedure code
     * @param string $procedureType Procedure type (e.g., 'CPT', 'HCPCS')
     * @param array $additionalData Additional line item data
     * @return array Created line item
     */
    public function addProcedure(
        int $appointmentId,
        string $code,
        string $procedureType = 'CPT',
        array $additionalData = []
    ): array {
        $data = array_merge([
            'appointment' => $appointmentId,
            'code' => $code,
            'procedure_type' => $procedureType,
        ], $additionalData);

        return $this->createLineItem($data);
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Consent Forms - Manage patient consent and authorization forms
 *
 * Consent forms track patient agreements for treatment, privacy practices,
 * financial responsibility, and other legal/compliance requirements.
 *
 * API Endpoint: /api/consent_forms
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#consent_forms
 */
class ConsentFormsResource extends AbstractResource
{
    protected string $resourcePath = '/api/consent_forms';

    /**
     * List consent forms
     *
     * @param array $filters Optional filters:
     *   - 'patient' (int): Filter by patient ID
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'since' (string): Filter by update date
     *   - 'date' (string): Filter by signed date
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific consent form
     *
     * @param int|string $consentId Consent form ID
     * @return array Consent form data
     */
    public function get(int|string $consentId): array
    {
        return parent::get($consentId);
    }

    /**
     * List consent forms for a specific patient
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
     * List consent forms for a specific doctor
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
     * Create a new consent form
     *
     * Required fields:
     * - patient (int): Patient ID
     * - title (string): Form title
     *
     * Optional fields:
     * - doctor (int): Doctor ID
     * - signed_date (string): Date signed (YYYY-MM-DD)
     * - content (string): Form content/text
     * - document (int): Associated document ID
     * - is_signed (bool): Whether form is signed
     *
     * @param array $data Consent form data
     * @return array Created consent form
     */
    public function createForm(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing consent form
     *
     * @param int $consentId Consent form ID
     * @param array $data Updated data
     * @return array Updated consent form
     */
    public function updateForm(int $consentId, array $data): array
    {
        return $this->update($consentId, $data);
    }

    /**
     * Delete a consent form
     *
     * @param int $consentId Consent form ID
     * @return void
     */
    public function deleteForm(int $consentId): void
    {
        $this->delete($consentId);
    }

    /**
     * Mark consent form as signed
     *
     * @param int $consentId Consent form ID
     * @param string|null $signedDate Date signed (defaults to today)
     * @return array Updated consent form
     */
    public function markAsSigned(int $consentId, ?string $signedDate = null): array
    {
        $data = [
            'is_signed' => true,
            'signed_date' => $signedDate ?? date('Y-m-d'),
        ];

        return $this->updateForm($consentId, $data);
    }

    /**
     * Get unsigned consent forms for a patient
     *
     * Useful for identifying required forms that need patient signature
     *
     * @param int $patientId Patient ID
     * @return array Unsigned forms
     */
    public function getUnsignedForms(int $patientId): array
    {
        $forms = $this->listByPatient($patientId);
        $unsigned = [];

        foreach ($forms as $form) {
            if (empty($form['is_signed'])) {
                $unsigned[] = $form;
            }
        }

        return $unsigned;
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Documents resource - manage patient documents and file uploads
 *
 * This resource handles uploading, retrieving, and managing patient documents
 * including lab results, medical records, consent forms, and other attachments.
 *
 * @see https://app.drchrono.com/api-docs/#tag/Documents Official API documentation
 */
class DocumentsResource extends AbstractResource
{
    protected string $resourcePath = '/api/documents';

    /**
     * List documents for a specific patient
     *
     * @param int $patientId Patient ID
     * @param array $filters Optional additional filters (since, doctor, date)
     * @return PagedCollection Paginated document results
     *
     * @example
     * // Get all documents for a patient
     * $documents = $client->documents->listByPatient(12345);
     *
     * // Get recent documents
     * $documents = $client->documents->listByPatient(12345, [
     *     'since' => '2024-01-01',
     * ]);
     *
     * foreach ($documents as $doc) {
     *     echo "{$doc['description']} - {$doc['date']}\n";
     * }
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * Upload a document to a patient's chart
     *
     * Supports common file types: PDF, PNG, JPG, DOCX, etc.
     *
     * @param int $doctorId Doctor ID who is uploading the document
     * @param int $patientId Patient ID to associate the document with
     * @param string $filePath Local file path to upload
     * @param string $description Description of the document
     * @param string $date Document date (YYYY-MM-DD format)
     * @param array $metatags Optional metadata tags for categorization
     * @return array Uploaded document data
     * @throws \InvalidArgumentException If file doesn't exist
     *
     * @example
     * // Upload a lab result PDF
     * $document = $client->documents->upload(
     *     doctorId: 123,
     *     patientId: 456,
     *     filePath: '/path/to/lab_result.pdf',
     *     description: 'Blood work results - CBC',
     *     date: '2024-03-15',
     *     metatags: ['Lab Results', 'CBC']
     * );
     *
     * echo "Uploaded document ID: {$document['id']}\n";
     *
     * // Upload a consent form
     * $consent = $client->documents->upload(
     *     doctorId: 123,
     *     patientId: 456,
     *     filePath: '/path/to/consent_signed.pdf',
     *     description: 'HIPAA Consent Form - Signed',
     *     date: date('Y-m-d'),
     *     metatags: ['Consent Forms', 'HIPAA']
     * );
     */
    public function upload(
        int $doctorId,
        int $patientId,
        string $filePath,
        string $description,
        string $date,
        array $metatags = []
    ): array {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("File not found: {$filePath}");
        }

        $data = [
            'doctor' => (string)$doctorId,
            'patient' => (string)$patientId,
            'description' => $description,
            'date' => $date,
        ];

        if (!empty($metatags)) {
            $data['metatags'] = json_encode($metatags);
        }

        return $this->httpClient->upload($this->resourcePath, $data, ['document' => $filePath]);
    }

    /**
     * Update document metadata
     */
    public function updateMetadata(int $documentId, array $metadata): array
    {
        return $this->update($documentId, $metadata);
    }

    /**
     * Delete document
     */
    public function deleteDocument(int $documentId): void
    {
        $this->delete($documentId);
    }

    /**
     * Download document content
     *
     * @param int $documentId Document ID
     * @return string Document URL or content
     *
     * @example
     * $documentUrl = $client->documents->download(98765);
     * echo "Download URL: {$documentUrl}\n";
     *
     * // Download and save locally
     * $content = file_get_contents($documentUrl);
     * file_put_contents('/path/to/save/document.pdf', $content);
     */
    public function download(int $documentId): string
    {
        $document = $this->get($documentId);
        return $document['document'] ?? '';
    }
}

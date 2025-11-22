<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Documents resource - manage patient documents and file uploads
 */
class DocumentsResource extends AbstractResource
{
    protected string $resourcePath = '/api/documents';

    /**
     * List documents for a patient
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * Upload document to patient chart
     * Required: doctor, patient, description, date
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
     * Download document
     */
    public function download(int $documentId): string
    {
        $document = $this->get($documentId);
        return $document['document'] ?? '';
    }
}

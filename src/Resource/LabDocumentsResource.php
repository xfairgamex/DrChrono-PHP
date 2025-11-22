<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Lab Documents resource - manage lab-related documents
 */
class LabDocumentsResource extends AbstractResource
{
    protected string $resourcePath = '/api/lab_documents';

    /**
     * List lab documents for patient
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * Get lab document
     */
    public function getDocument(int $documentId): array
    {
        return $this->get($documentId);
    }

    /**
     * Upload lab document
     */
    public function uploadDocument(array $documentData): array
    {
        return $this->create($documentData);
    }
}

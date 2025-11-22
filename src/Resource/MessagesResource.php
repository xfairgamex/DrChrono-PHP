<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Messages resource - manage patient messages
 */
class MessagesResource extends AbstractResource
{
    protected string $resourcePath = '/api/messages';

    /**
     * List messages by patient
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * Send message
     */
    public function send(array $messageData): array
    {
        return $this->create($messageData);
    }
}

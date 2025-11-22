<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Offices resource - manage office locations
 */
class OfficesResource extends AbstractResource
{
    protected string $resourcePath = '/api/offices';

    /**
     * List all offices
     */
    public function listAll(): PagedCollection
    {
        return $this->list();
    }

    /**
     * Get office details
     */
    public function getOffice(int $officeId): array
    {
        return $this->get($officeId);
    }

    /**
     * Get online scheduling settings for office
     */
    public function getOnlineScheduling(int $officeId): array
    {
        return $this->httpClient->get("/api/offices/{$officeId}/online_scheduling");
    }

    /**
     * Add exam room to office
     */
    public function addExamRoom(int $officeId, string $name, int $index = 0): array
    {
        return $this->httpClient->post('/api/office_exam_rooms', [
            'office' => $officeId,
            'name' => $name,
            'index' => $index,
        ]);
    }

    /**
     * List exam rooms for office
     */
    public function listExamRooms(int $officeId): array
    {
        return $this->httpClient->get('/api/office_exam_rooms', ['office' => $officeId]);
    }
}

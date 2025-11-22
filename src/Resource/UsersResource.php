<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Users resource - manage doctors and staff users
 */
class UsersResource extends AbstractResource
{
    protected string $resourcePath = '/api/users';

    /**
     * Get current authenticated user
     */
    public function getCurrent(): array
    {
        return $this->httpClient->get('/api/users/current');
    }

    /**
     * List all users (doctors/staff)
     */
    public function listAll(): PagedCollection
    {
        return $this->list();
    }

    /**
     * Get specific user/doctor
     */
    public function getUser(int $userId): array
    {
        return $this->get($userId);
    }

    /**
     * List doctors only
     */
    public function listDoctors(): PagedCollection
    {
        return $this->list(['is_staff' => 'false']);
    }

    /**
     * Get doctor's fee schedule
     */
    public function getFeeSchedule(int $doctorId): array
    {
        return $this->httpClient->get('/api/fee_schedules', ['doctor' => $doctorId]);
    }
}

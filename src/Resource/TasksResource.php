<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Tasks resource - manage tasks and to-do items
 */
class TasksResource extends AbstractResource
{
    protected string $resourcePath = '/api/tasks';

    /**
     * List tasks with filters
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * List tasks by assignee
     */
    public function listByAssignee(int $userId, array $filters = []): PagedCollection
    {
        $filters['assignee' ] = $userId;
        return $this->list($filters);
    }

    /**
     * List tasks by status
     */
    public function listByStatus(string $status, array $filters = []): PagedCollection
    {
        $filters['status'] = $status;
        return $this->list($filters);
    }

    /**
     * Create task
     */
    public function createTask(array $taskData): array
    {
        return $this->create($taskData);
    }

    /**
     * Update task
     */
    public function updateTask(int $taskId, array $taskData): array
    {
        return $this->update($taskId, $taskData);
    }

    /**
     * Mark task as complete
     */
    public function markComplete(int $taskId): array
    {
        return $this->update($taskId, ['status' => 'Completed']);
    }

    /**
     * Get task categories
     */
    public function getCategories(): array
    {
        return $this->httpClient->get('/api/task_categories');
    }

    /**
     * Get task statuses
     */
    public function getStatuses(): array
    {
        return $this->httpClient->get('/api/task_statuses');
    }

    /**
     * Get task notes
     */
    public function getNotes(int $taskId): array
    {
        return $this->httpClient->get('/api/task_notes', ['task' => $taskId]);
    }

    /**
     * Add note to task
     */
    public function addNote(int $taskId, string $note): array
    {
        return $this->httpClient->post('/api/task_notes', [
            'task' => $taskId,
            'note' => $note,
        ]);
    }
}

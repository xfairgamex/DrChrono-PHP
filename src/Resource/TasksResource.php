<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Tasks resource - manage tasks and to-do items
 *
 * This resource provides methods for creating, assigning, and tracking tasks
 * within the practice management system.
 *
 * @see https://app.drchrono.com/api-docs/#tag/Tasks Official API documentation
 */
class TasksResource extends AbstractResource
{
    protected string $resourcePath = '/api/tasks';

    /**
     * List tasks for a specific patient
     *
     * @param int $patientId Patient ID
     * @param array $filters Optional additional filters (status, assignee, since)
     * @return PagedCollection Paginated task results
     *
     * @example
     * // Get all tasks for a patient
     * $tasks = $client->tasks->listByPatient(12345);
     *
     * // Get pending tasks only
     * $tasks = $client->tasks->listByPatient(12345, [
     *     'status' => 'Pending',
     * ]);
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * List tasks assigned to a specific user
     *
     * @param int $userId User ID (doctor or staff member)
     * @param array $filters Optional additional filters (status, patient, since)
     * @return PagedCollection Paginated task results
     *
     * @example
     * // Get all tasks assigned to a user
     * $tasks = $client->tasks->listByAssignee(123);
     *
     * // Get incomplete tasks for today
     * $tasks = $client->tasks->listByAssignee(123, [
     *     'status' => 'Pending',
     *     'since' => date('Y-m-d'),
     * ]);
     *
     * foreach ($tasks as $task) {
     *     echo "Task: {$task['title']}\n";
     *     echo "Due: {$task['due_at']}\n";
     * }
     */
    public function listByAssignee(int $userId, array $filters = []): PagedCollection
    {
        $filters['assignee'] = $userId;
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
     * Create a new task
     *
     * @param array $taskData Task data
     * @return array Created task data
     *
     * Required fields:
     * - title (string): Task title/description
     * - assignee (int): User ID to assign the task to
     *
     * @example
     * // Create a simple task
     * $task = $client->tasks->createTask([
     *     'title' => 'Review lab results for patient',
     *     'assignee' => 123,
     *     'patient' => 456,
     *     'due_at' => '2024-03-20T17:00:00',
     *     'priority' => 'High',
     * ]);
     *
     * echo "Created task ID: {$task['id']}\n";
     *
     * // Create task with category and notes
     * $task = $client->tasks->createTask([
     *     'title' => 'Call patient about appointment',
     *     'assignee' => 123,
     *     'patient' => 789,
     *     'category' => 5,  // Task category ID
     *     'notes' => 'Patient requested callback before 3pm',
     *     'due_at' => date('Y-m-d') . 'T15:00:00',
     * ]);
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
     * Mark a task as complete
     *
     * @param int $taskId Task ID
     * @return array Updated task data
     *
     * @example
     * $task = $client->tasks->markComplete(12345);
     * echo "Task status: {$task['status']}\n";  // "Completed"
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
     * Add a note to a task
     *
     * @param int $taskId Task ID
     * @param string $note Note content
     * @return array Created note data
     *
     * @example
     * $note = $client->tasks->addNote(12345, 'Contacted patient, left voicemail');
     * echo "Note added at: {$note['created_at']}\n";
     */
    public function addNote(int $taskId, string $note): array
    {
        return $this->httpClient->post('/api/task_notes', [
            'task' => $taskId,
            'note' => $note,
        ]);
    }
}

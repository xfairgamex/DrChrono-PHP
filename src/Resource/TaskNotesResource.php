<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Task Notes - Add detailed notes to tasks for documentation
 *
 * Task notes provide detailed documentation and collaborative communication
 * regarding task progress, decisions, and updates.
 *
 * API Endpoint: /api/task_notes
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#task_notes
 */
class TaskNotesResource extends AbstractResource
{
    protected string $resourcePath = '/api/task_notes';

    /**
     * List task notes
     *
     * @param array $filters Optional filters:
     *   - 'task' (int): Filter by task ID
     *   - 'author' (int): Filter by author user ID
     *   - 'since' (string): Filter by update date
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get a specific task note
     *
     * @param int|string $noteId Note ID
     * @return array Note data
     */
    public function get(int|string $noteId): array
    {
        return parent::get($noteId);
    }

    /**
     * List notes for a specific task
     *
     * @param int $taskId Task ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByTask(int $taskId, array $filters = []): PagedCollection
    {
        $filters['task'] = $taskId;
        return $this->list($filters);
    }

    /**
     * List notes by author
     *
     * @param int $userId User ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByAuthor(int $userId, array $filters = []): PagedCollection
    {
        $filters['author'] = $userId;
        return $this->list($filters);
    }

    /**
     * Create a new task note
     *
     * Required fields:
     * - task (int): Task ID
     * - content (string): Note content
     *
     * Optional fields:
     * - author (int): Author user ID (defaults to current user)
     * - is_pinned (bool): Pin note to top
     * - attachments (array): File attachments
     *
     * @param array $data Note data
     * @return array Created note
     */
    public function createNote(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update an existing task note
     *
     * @param int $noteId Note ID
     * @param array $data Updated data
     * @return array Updated note
     */
    public function updateNote(int $noteId, array $data): array
    {
        return $this->update($noteId, $data);
    }

    /**
     * Delete a task note
     *
     * @param int $noteId Note ID
     * @return void
     */
    public function deleteNote(int $noteId): void
    {
        $this->delete($noteId);
    }

    /**
     * Add a quick note to a task
     *
     * Convenience method for creating a simple text note
     *
     * @param int $taskId Task ID
     * @param string $content Note content
     * @return array Created note
     */
    public function addQuickNote(int $taskId, string $content): array
    {
        return $this->createNote([
            'task' => $taskId,
            'content' => $content,
        ]);
    }

    /**
     * Pin a note
     *
     * @param int $noteId Note ID
     * @return array Updated note
     */
    public function pin(int $noteId): array
    {
        return $this->updateNote($noteId, ['is_pinned' => true]);
    }

    /**
     * Unpin a note
     *
     * @param int $noteId Note ID
     * @return array Updated note
     */
    public function unpin(int $noteId): array
    {
        return $this->updateNote($noteId, ['is_pinned' => false]);
    }

    /**
     * Get pinned notes for a task
     *
     * @param int $taskId Task ID
     * @return PagedCollection
     */
    public function getPinnedNotes(int $taskId): PagedCollection
    {
        return $this->list([
            'task' => $taskId,
            'is_pinned' => 'true',
        ]);
    }

    /**
     * Get task's note history ordered by date
     *
     * @param int $taskId Task ID
     * @return PagedCollection
     */
    public function getTaskHistory(int $taskId): PagedCollection
    {
        return $this->list([
            'task' => $taskId,
            'ordering' => '-created_at',
        ]);
    }

    /**
     * Get recent notes across all tasks
     *
     * @param int $limit Maximum number of notes to retrieve
     * @return PagedCollection
     */
    public function getRecent(int $limit = 50): PagedCollection
    {
        return $this->list([
            'ordering' => '-created_at',
            'page_size' => $limit,
        ]);
    }
}

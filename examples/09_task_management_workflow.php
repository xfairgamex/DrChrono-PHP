<?php

/**
 * Task Management Workflow Example
 *
 * This example demonstrates advanced task management features including:
 * - Task templates for common workflows
 * - Task categories and custom statuses
 * - Bulk task operations
 * - Task notes and collaboration
 * - Automated task assignment
 *
 * Use case: Medical practice implementing automated task workflows
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;
use DrChrono\Exception\ApiException;

// Initialize client
$client = DrChronoClient::withAccessToken($_ENV['DRCHRONO_ACCESS_TOKEN'] ?? 'your_access_token');

/**
 * Setup: Create Task Categories and Statuses
 */
function setupTaskSystem($client): array
{
    echo "=================================================\n";
    echo "SETTING UP TASK MANAGEMENT SYSTEM\n";
    echo "=================================================\n\n";

    // Create task categories
    $categories = [
        ['name' => 'Follow-up', 'color' => '#4A90E2', 'is_active' => true],
        ['name' => 'Billing', 'color' => '#7ED321', 'is_active' => true],
        ['name' => 'Lab Results', 'color' => '#F5A623', 'is_active' => true],
        ['name' => 'Prescriptions', 'color' => '#BD10E0', 'is_active' => true],
        ['name' => 'Administrative', 'color' => '#50E3C2', 'is_active' => true],
    ];

    echo "Step 1: Creating task categories...\n";
    $createdCategories = [];

    foreach ($categories as $category) {
        try {
            $created = $client->taskCategories->createCategory($category);
            echo "  ✓ Created category: {$category['name']} (ID: {$created['id']})\n";
            $createdCategories[$category['name']] = $created;
        } catch (ApiException $e) {
            echo "  ! Category may already exist: {$category['name']}\n";
        }
    }

    // Create custom task statuses
    $statuses = [
        ['name' => 'New', 'is_active' => true, 'is_completion_status' => false],
        ['name' => 'In Progress', 'is_active' => true, 'is_completion_status' => false],
        ['name' => 'Waiting', 'is_active' => true, 'is_completion_status' => false],
        ['name' => 'Completed', 'is_active' => true, 'is_completion_status' => true],
        ['name' => 'Cancelled', 'is_active' => true, 'is_completion_status' => true],
    ];

    echo "\nStep 2: Creating task statuses...\n";
    $createdStatuses = [];

    foreach ($statuses as $status) {
        try {
            $created = $client->taskStatuses->createStatus($status);
            echo "  ✓ Created status: {$status['name']} (ID: {$created['id']})\n";
            $createdStatuses[$status['name']] = $created;
        } catch (ApiException $e) {
            echo "  ! Status may already exist: {$status['name']}\n";
        }
    }

    return [
        'categories' => $createdCategories,
        'statuses' => $createdStatuses,
    ];
}

/**
 * Create Task Templates for Common Workflows
 */
function createTaskTemplates($client, array $categories, int $doctorId): array
{
    echo "\n=================================================\n";
    echo "CREATING TASK TEMPLATES\n";
    echo "=================================================\n\n";

    $templates = [
        [
            'title' => 'New Patient Follow-up Call',
            'description' => 'Call patient 24-48 hours after first visit to ensure satisfaction and answer questions',
            'category' => $categories['Follow-up']['id'] ?? null,
            'priority' => 'Medium',
            'estimated_time' => 15,
            'doctor' => $doctorId,
        ],
        [
            'title' => 'Lab Results Review',
            'description' => 'Review lab results and notify patient. Document in clinical notes.',
            'category' => $categories['Lab Results']['id'] ?? null,
            'priority' => 'High',
            'estimated_time' => 10,
            'doctor' => $doctorId,
        ],
        [
            'title' => 'Insurance Verification',
            'description' => 'Verify patient insurance eligibility before scheduled appointment',
            'category' => $categories['Billing']['id'] ?? null,
            'priority' => 'High',
            'estimated_time' => 5,
            'doctor' => $doctorId,
        ],
        [
            'title' => 'Prescription Refill Request',
            'description' => 'Review patient chart and approve/deny refill request',
            'category' => $categories['Prescriptions']['id'] ?? null,
            'priority' => 'Medium',
            'estimated_time' => 5,
            'doctor' => $doctorId,
        ],
    ];

    $createdTemplates = [];

    foreach ($templates as $template) {
        try {
            $created = $client->taskTemplates->createTemplate($template);
            echo "✓ Created template: {$template['title']} (ID: {$created['id']})\n";
            $createdTemplates[$template['title']] = $created;
        } catch (ApiException $e) {
            echo "✗ Failed to create template: {$template['title']}\n";
            echo "  Error: {$e->getMessage()}\n";
        }
    }

    return $createdTemplates;
}

/**
 * Automated Task Creation from Appointment
 */
function createTasksFromAppointment($client, int $appointmentId, array $templates): void
{
    echo "\n=================================================\n";
    echo "AUTO-CREATING TASKS FROM APPOINTMENT\n";
    echo "=================================================\n\n";

    try {
        // Get appointment details
        $appointment = $client->appointments->get($appointmentId);
        echo "Appointment #{$appointmentId}: {$appointment['status']}\n";
        echo "Patient: {$appointment['patient']}\n\n";

        // If appointment is marked complete, create follow-up tasks
        if ($appointment['status'] === 'Complete') {
            echo "Appointment completed - creating follow-up tasks...\n\n";

            // Task 1: Follow-up call (use template)
            $followUpTask = $client->taskTemplates->instantiateTemplate(
                $templates['New Patient Follow-up Call']['id'],
                [
                    'patient' => $appointment['patient'],
                    'appointment' => $appointmentId,
                    'due_date' => date('Y-m-d', strtotime('+2 days')),
                ]
            );
            echo "✓ Created task: Follow-up call (ID: {$followUpTask['id']})\n";

            // Task 2: Insurance claim submission
            $billingTask = $client->tasks->createTask([
                'title' => 'Submit insurance claim',
                'patient' => $appointment['patient'],
                'appointment' => $appointmentId,
                'category' => 'Billing',
                'priority' => 'High',
                'due_date' => date('Y-m-d', strtotime('+3 days')),
                'notes' => 'Submit claim for appointment #' . $appointmentId,
            ]);
            echo "✓ Created task: Submit insurance claim (ID: {$billingTask['id']})\n";

            // Task 3: Check for ordered labs
            $labTask = $client->tasks->createTask([
                'title' => 'Check lab order status',
                'patient' => $appointment['patient'],
                'appointment' => $appointmentId,
                'category' => 'Lab Results',
                'priority' => 'Medium',
                'due_date' => date('Y-m-d', strtotime('+7 days')),
                'notes' => 'Verify all ordered labs have been completed and results reviewed',
            ]);
            echo "✓ Created task: Check lab order status (ID: {$labTask['id']})\n";
        }

    } catch (ApiException $e) {
        echo "✗ Error creating tasks: {$e->getMessage()}\n";
    }
}

/**
 * Task Assignment and Workflow
 */
function demonstrateTaskWorkflow($client, int $taskId, int $userId): void
{
    echo "\n=================================================\n";
    echo "TASK WORKFLOW DEMONSTRATION\n";
    echo "=================================================\n\n";

    try {
        // Get task
        $task = $client->tasks->get($taskId);
        echo "Task: {$task['title']}\n";
        echo "Status: {$task['status']}\n\n";

        // Step 1: Assign task
        echo "Step 1: Assigning task to user #{$userId}...\n";
        $updated = $client->tasks->assignTask($taskId, $userId);
        echo "  ✓ Task assigned\n\n";

        // Step 2: Add initial note
        echo "Step 2: Adding initial note...\n";
        $note = $client->taskNotes->addQuickNote($taskId, $userId, 'Starting work on this task');
        echo "  ✓ Note added (ID: {$note['id']})\n\n";

        // Step 3: Update status to In Progress
        echo "Step 3: Updating status to 'In Progress'...\n";
        $updated = $client->tasks->updateTask($taskId, ['status' => 'In Progress']);
        echo "  ✓ Status updated\n\n";

        // Step 4: Simulate work and add progress note
        echo "Step 4: Adding progress note...\n";
        $progressNote = $client->taskNotes->createNote([
            'task' => $taskId,
            'author' => $userId,
            'content' => 'Called patient. Left voicemail. Will try again tomorrow.',
        ]);
        echo "  ✓ Progress note added\n\n";

        // Step 5: Complete task
        echo "Step 5: Marking task as complete...\n";
        $completed = $client->tasks->markComplete($taskId);
        echo "  ✓ Task completed\n\n";

        // Step 6: Add completion note
        echo "Step 6: Adding completion note...\n";
        $completionNote = $client->taskNotes->createNote([
            'task' => $taskId,
            'author' => $userId,
            'content' => 'Successfully reached patient. All questions answered. Patient satisfied with care.',
        ]);
        echo "  ✓ Completion note added\n\n";

        // Get task history
        echo "Task History:\n";
        $history = $client->taskNotes->getTaskHistory($taskId);
        foreach ($history as $entry) {
            echo "  - [{$entry['created_at']}] {$entry['content']}\n";
        }

    } catch (ApiException $e) {
        echo "✗ Error in workflow: {$e->getMessage()}\n";
    }
}

/**
 * Bulk Task Operations
 */
function bulkTaskOperations($client, int $doctorId, string $category): void
{
    echo "\n=================================================\n";
    echo "BULK TASK OPERATIONS\n";
    echo "=================================================\n\n";

    try {
        // Get all overdue tasks for a category
        echo "Finding overdue tasks in '{$category}' category...\n";
        $tasks = $client->tasks->listByStatus('Open', [
            'doctor' => $doctorId,
            'category' => $category,
        ]);

        $overdueTasks = [];
        $today = date('Y-m-d');

        foreach ($tasks as $task) {
            if (isset($task['due_date']) && $task['due_date'] < $today) {
                $overdueTasks[] = $task;
            }
        }

        echo "Found " . count($overdueTasks) . " overdue tasks\n\n";

        // Bulk update priorities
        if (count($overdueTasks) > 0) {
            echo "Updating priority to 'High' for overdue tasks...\n";
            foreach ($overdueTasks as $task) {
                try {
                    $client->tasks->updateTask($task['id'], [
                        'priority' => 'High'
                    ]);
                    echo "  ✓ Updated task #{$task['id']}: {$task['title']}\n";
                } catch (ApiException $e) {
                    echo "  ✗ Failed to update task #{$task['id']}\n";
                }
            }
        }

        // Add reminder notes to overdue tasks
        if (count($overdueTasks) > 0) {
            echo "\nAdding reminder notes to overdue tasks...\n";
            foreach ($overdueTasks as $task) {
                try {
                    $client->taskNotes->createNote([
                        'task' => $task['id'],
                        'content' => 'REMINDER: This task is overdue. Please prioritize completion.',
                    ]);
                    echo "  ✓ Added reminder to task #{$task['id']}\n";
                } catch (ApiException $e) {
                    echo "  ✗ Failed to add reminder to task #{$task['id']}\n";
                }
            }
        }

    } catch (ApiException $e) {
        echo "✗ Error in bulk operations: {$e->getMessage()}\n";
    }
}

/**
 * Task Analytics and Reporting
 */
function generateTaskReport($client, int $doctorId, string $startDate, string $endDate): array
{
    echo "\n=================================================\n";
    echo "TASK ANALYTICS REPORT\n";
    echo "=================================================\n\n";
    echo "Period: {$startDate} to {$endDate}\n\n";

    try {
        // Get all tasks in date range
        $allTasks = $client->tasks->listByDoctor($doctorId, [
            'since' => $startDate,
        ]);

        $analytics = [
            'total_tasks' => 0,
            'completed_tasks' => 0,
            'open_tasks' => 0,
            'overdue_tasks' => 0,
            'by_category' => [],
            'by_priority' => ['High' => 0, 'Medium' => 0, 'Low' => 0],
            'avg_completion_time' => 0,
        ];

        $today = date('Y-m-d');
        $completionTimes = [];

        foreach ($allTasks as $task) {
            $analytics['total_tasks']++;

            // Status counts
            if ($task['status'] === 'Completed') {
                $analytics['completed_tasks']++;

                // Calculate completion time
                if (isset($task['created_at']) && isset($task['completed_at'])) {
                    $created = new DateTime($task['created_at']);
                    $completed = new DateTime($task['completed_at']);
                    $completionTimes[] = $created->diff($completed)->days;
                }
            } else {
                $analytics['open_tasks']++;

                // Check if overdue
                if (isset($task['due_date']) && $task['due_date'] < $today) {
                    $analytics['overdue_tasks']++;
                }
            }

            // Category counts
            $category = $task['category'] ?? 'Uncategorized';
            if (!isset($analytics['by_category'][$category])) {
                $analytics['by_category'][$category] = 0;
            }
            $analytics['by_category'][$category]++;

            // Priority counts
            $priority = $task['priority'] ?? 'Medium';
            if (isset($analytics['by_priority'][$priority])) {
                $analytics['by_priority'][$priority]++;
            }
        }

        // Calculate average completion time
        if (count($completionTimes) > 0) {
            $analytics['avg_completion_time'] = array_sum($completionTimes) / count($completionTimes);
        }

        // Calculate completion rate
        $analytics['completion_rate'] = $analytics['total_tasks'] > 0
            ? ($analytics['completed_tasks'] / $analytics['total_tasks']) * 100
            : 0;

        // Print report
        echo "Summary:\n";
        echo "  - Total Tasks: {$analytics['total_tasks']}\n";
        echo "  - Completed: {$analytics['completed_tasks']}\n";
        echo "  - Open: {$analytics['open_tasks']}\n";
        echo "  - Overdue: {$analytics['overdue_tasks']}\n";
        echo "  - Completion Rate: " . round($analytics['completion_rate'], 1) . "%\n";
        echo "  - Avg Completion Time: " . round($analytics['avg_completion_time'], 1) . " days\n\n";

        echo "By Category:\n";
        foreach ($analytics['by_category'] as $category => $count) {
            echo "  - {$category}: {$count}\n";
        }

        echo "\nBy Priority:\n";
        foreach ($analytics['by_priority'] as $priority => $count) {
            echo "  - {$priority}: {$count}\n";
        }

        return $analytics;

    } catch (ApiException $e) {
        echo "✗ Error generating report: {$e->getMessage()}\n";
        return [];
    }
}

/**
 * Team Task Dashboard
 */
function generateTeamDashboard($client, array $userIds): void
{
    echo "\n=================================================\n";
    echo "TEAM TASK DASHBOARD\n";
    echo "=================================================\n\n";

    foreach ($userIds as $userId) {
        try {
            $tasks = $client->tasks->listByAssignee($userId, [
                'status' => 'Open',
            ]);

            $taskCount = 0;
            $highPriority = 0;
            $overdue = 0;
            $today = date('Y-m-d');

            foreach ($tasks as $task) {
                $taskCount++;

                if (($task['priority'] ?? '') === 'High') {
                    $highPriority++;
                }

                if (isset($task['due_date']) && $task['due_date'] < $today) {
                    $overdue++;
                }
            }

            echo "User #{$userId}:\n";
            echo "  - Total Open Tasks: {$taskCount}\n";
            echo "  - High Priority: {$highPriority}\n";
            echo "  - Overdue: {$overdue}\n";
            echo "  - On Track: " . ($taskCount - $overdue) . "\n\n";

        } catch (ApiException $e) {
            echo "✗ Error loading tasks for user #{$userId}: {$e->getMessage()}\n\n";
        }
    }
}

// ========================================
// Example Usage
// ========================================

if (php_sapi_name() === 'cli') {
    echo "DrChrono PHP SDK - Task Management Workflow Example\n\n";

    // Replace these with actual IDs from your DrChrono account
    $doctorId = 123456;
    $appointmentId = 789012;
    $userId = 345678;

    try {
        // Step 1: Setup task system
        $setup = setupTaskSystem($client);

        // Step 2: Create templates
        $templates = createTaskTemplates($client, $setup['categories'], $doctorId);

        // Step 3: Auto-create tasks from appointment
        createTasksFromAppointment($client, $appointmentId, $templates);

        // Step 4: Demonstrate workflow (get a task ID from your system)
        // $taskId = 999999;
        // demonstrateTaskWorkflow($client, $taskId, $userId);

        // Step 5: Bulk operations
        bulkTaskOperations($client, $doctorId, 'Follow-up');

        // Step 6: Generate analytics
        $report = generateTaskReport(
            $client,
            $doctorId,
            date('Y-m-d', strtotime('-30 days')),
            date('Y-m-d')
        );

        // Step 7: Team dashboard
        generateTeamDashboard($client, [$userId]);

    } catch (ApiException $e) {
        echo "\n✗ Error: {$e->getMessage()}\n";
        echo "Status Code: {$e->getStatusCode()}\n";
    }

    echo "\n=================================================\n";
    echo "TASK MANAGEMENT WORKFLOW COMPLETE\n";
    echo "=================================================\n\n";
}

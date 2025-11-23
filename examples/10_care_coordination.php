<?php

/**
 * Care Coordination Workflow Example
 *
 * This example demonstrates comprehensive care coordination including:
 * - Care plan management
 * - Patient risk assessments
 * - Intervention tracking
 * - Team communication logs
 * - Preventive care reminders
 * - Care team collaboration
 *
 * Use case: Coordinated care for chronic disease management
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;
use DrChrono\Exception\ApiException;

// Initialize client
$client = DrChronoClient::withAccessToken($_ENV['DRCHRONO_ACCESS_TOKEN'] ?? 'your_access_token');

/**
 * Step 1: Conduct Patient Risk Assessment
 */
function conductRiskAssessment($client, int $patientId, int $doctorId): array
{
    echo "=================================================\n";
    echo "STEP 1: PATIENT RISK ASSESSMENT\n";
    echo "=================================================\n\n";

    try {
        // Get patient information
        $patient = $client->patients->get($patientId);
        echo "Patient: {$patient['first_name']} {$patient['last_name']}\n";
        echo "DOB: {$patient['date_of_birth']}\n\n";

        // Create comprehensive risk assessment
        $assessment = $client->patientRiskAssessments->createAssessment([
            'patient' => $patientId,
            'doctor' => $doctorId,
            'assessment_type' => 'Chronic Disease',
            'assessment_date' => date('Y-m-d'),
            'risk_level' => 'High',
            'risk_factors' => json_encode([
                'Diabetes Type 2',
                'Hypertension',
                'Obesity (BMI > 30)',
                'Family history of heart disease',
                'Sedentary lifestyle'
            ]),
            'notes' => 'Patient has multiple chronic conditions requiring coordinated care management.',
        ]);

        echo "✓ Risk assessment completed\n";
        echo "  - Assessment ID: {$assessment['id']}\n";
        echo "  - Risk Level: {$assessment['risk_level']}\n";
        echo "  - Identified risk factors:\n";

        $factors = json_decode($assessment['risk_factors'], true);
        foreach ($factors as $factor) {
            echo "    • {$factor}\n";
        }

        return $assessment;

    } catch (ApiException $e) {
        echo "✗ Error creating risk assessment: {$e->getMessage()}\n";
        return [];
    }
}

/**
 * Step 2: Create Comprehensive Care Plan
 */
function createCarePlan($client, int $patientId, int $doctorId, array $riskAssessment): array
{
    echo "\n=================================================\n";
    echo "STEP 2: CREATE COMPREHENSIVE CARE PLAN\n";
    echo "=================================================\n\n";

    try {
        // Create care plan based on risk assessment
        $carePlan = $client->carePlans->createCarePlan([
            'patient' => $patientId,
            'doctor' => $doctorId,
            'title' => 'Diabetes & Hypertension Management Plan',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+12 months')),
            'status' => 'Active',
            'description' => 'Comprehensive care plan for managing diabetes and hypertension with focus on lifestyle modifications and medication compliance.',
        ]);

        echo "✓ Care plan created (ID: {$carePlan['id']})\n";
        echo "  - Title: {$carePlan['title']}\n";
        echo "  - Duration: {$carePlan['start_date']} to {$carePlan['end_date']}\n\n";

        // Add goals to care plan
        echo "Adding care plan goals...\n";
        $goals = [
            'Achieve HbA1c < 7.0% within 6 months',
            'Reduce blood pressure to < 130/80 mmHg',
            'Lose 10% body weight within 12 months',
            'Exercise 30 minutes 5x per week',
            'Medication adherence > 95%'
        ];

        foreach ($goals as $goal) {
            $client->carePlans->addGoal($carePlan['id'], [
                'description' => $goal,
                'target_date' => date('Y-m-d', strtotime('+6 months')),
            ]);
            echo "  ✓ Added goal: {$goal}\n";
        }

        return $carePlan;

    } catch (ApiException $e) {
        echo "✗ Error creating care plan: {$e->getMessage()}\n";
        return [];
    }
}

/**
 * Step 3: Define Care Interventions
 */
function defineInterventions($client, int $patientId, int $doctorId, int $carePlanId): array
{
    echo "\n=================================================\n";
    echo "STEP 3: DEFINE CARE INTERVENTIONS\n";
    echo "=================================================\n\n";

    $interventions = [
        [
            'title' => 'Diabetes Education Program',
            'type' => 'Education',
            'frequency' => 'Monthly',
            'duration_weeks' => 12,
            'description' => 'Structured diabetes self-management education including nutrition, glucose monitoring, and medication management.',
        ],
        [
            'title' => 'Nutritionist Consultation',
            'type' => 'Consultation',
            'frequency' => 'Bi-weekly',
            'duration_weeks' => 12,
            'description' => 'One-on-one nutritional counseling focused on diabetes-friendly diet and weight management.',
        ],
        [
            'title' => 'Home Blood Pressure Monitoring',
            'type' => 'Monitoring',
            'frequency' => 'Daily',
            'duration_weeks' => 52,
            'description' => 'Daily home BP monitoring with weekly review by care team.',
        ],
        [
            'title' => 'Physical Activity Program',
            'type' => 'Lifestyle',
            'frequency' => 'Weekly',
            'duration_weeks' => 52,
            'description' => 'Supervised exercise program progressing from walking to moderate-intensity activities.',
        ],
        [
            'title' => 'Medication Reconciliation',
            'type' => 'Medication',
            'frequency' => 'Monthly',
            'duration_weeks' => 12,
            'description' => 'Monthly review of medications for adherence, side effects, and effectiveness.',
        ],
    ];

    $createdInterventions = [];

    foreach ($interventions as $intervention) {
        try {
            $created = $client->patientInterventions->createIntervention([
                'patient' => $patientId,
                'doctor' => $doctorId,
                'care_plan' => $carePlanId,
                'intervention_type' => $intervention['type'],
                'title' => $intervention['title'],
                'description' => $intervention['description'],
                'frequency' => $intervention['frequency'],
                'start_date' => date('Y-m-d'),
                'end_date' => date('Y-m-d', strtotime("+{$intervention['duration_weeks']} weeks")),
                'status' => 'Active',
            ]);

            echo "✓ Intervention created: {$intervention['title']}\n";
            echo "  - Type: {$intervention['type']}\n";
            echo "  - Frequency: {$intervention['frequency']}\n";
            echo "  - Duration: {$intervention['duration_weeks']} weeks\n\n";

            $createdInterventions[] = $created;

        } catch (ApiException $e) {
            echo "✗ Failed to create intervention: {$intervention['title']}\n";
            echo "  Error: {$e->getMessage()}\n\n";
        }
    }

    return $createdInterventions;
}

/**
 * Step 4: Schedule Follow-up Appointments
 */
function scheduleFollowUps($client, int $patientId, int $doctorId, int $officeId): array
{
    echo "=================================================\n";
    echo "STEP 4: SCHEDULE FOLLOW-UP APPOINTMENTS\n";
    echo "=================================================\n\n";

    $appointments = [
        ['weeks' => 2, 'duration' => 30, 'reason' => 'Initial care plan review'],
        ['weeks' => 6, 'duration' => 30, 'reason' => 'Progress check - Labs review'],
        ['weeks' => 12, 'duration' => 30, 'reason' => 'Quarterly assessment'],
        ['weeks' => 24, 'duration' => 30, 'reason' => '6-month comprehensive review'],
    ];

    $scheduledAppointments = [];

    foreach ($appointments as $apt) {
        try {
            $scheduledTime = date('Y-m-d\TH:i:s', strtotime("+{$apt['weeks']} weeks 10:00"));

            $appointment = $client->appointments->createAppointment([
                'doctor' => $doctorId,
                'patient' => $patientId,
                'office' => $officeId,
                'duration' => $apt['duration'],
                'scheduled_time' => $scheduledTime,
                'status' => 'Scheduled',
                'reason' => $apt['reason'],
            ]);

            echo "✓ Scheduled: {$apt['reason']}\n";
            echo "  - Date: " . date('Y-m-d', strtotime("+{$apt['weeks']} weeks")) . "\n";
            echo "  - Duration: {$apt['duration']} minutes\n\n";

            $scheduledAppointments[] = $appointment;

        } catch (ApiException $e) {
            echo "✗ Failed to schedule appointment: {$apt['reason']}\n";
            echo "  Error: {$e->getMessage()}\n\n";
        }
    }

    return $scheduledAppointments;
}

/**
 * Step 5: Setup Communication Protocols
 */
function setupCommunicationProtocols($client, int $patientId, int $doctorId): void
{
    echo "=================================================\n";
    echo "STEP 5: COMMUNICATION PROTOCOLS\n";
    echo "=================================================\n\n";

    // Document initial patient communication
    echo "Documenting initial patient contact...\n";

    try {
        // Log initial care plan discussion
        $commLog1 = $client->commLogs->logPhoneCall(
            patientId: $patientId,
            doctorId: $doctorId,
            direction: 'Outbound',
            durationMinutes: 15,
            subject: 'Care Plan Introduction',
            notes: 'Discussed comprehensive care plan with patient. Reviewed goals, interventions, and follow-up schedule. Patient verbalized understanding and commitment to plan.'
        );
        echo "  ✓ Logged initial phone consultation (ID: {$commLog1['id']})\n";

        // Send patient educational materials
        $message = $client->patientMessages->sendMessage([
            'patient' => $patientId,
            'subject' => 'Your Personalized Care Plan',
            'message' => "Dear Patient,\n\nWe've created a comprehensive care plan to help you manage your diabetes and hypertension. Your care team is here to support you every step of the way.\n\nKey points:\n- Regular monitoring of blood sugar and blood pressure\n- Monthly education sessions\n- Nutritional counseling\n- Exercise program\n- Regular follow-up appointments\n\nPlease don't hesitate to reach out if you have any questions.\n\nBest regards,\nYour Care Team",
        ]);
        echo "  ✓ Sent care plan overview message (ID: {$message['id']})\n";

        // Create task for care coordinator follow-up
        $task = $client->tasks->createTask([
            'title' => 'Care Coordinator: Initial patient outreach',
            'patient' => $patientId,
            'category' => 'Care Coordination',
            'priority' => 'High',
            'due_date' => date('Y-m-d', strtotime('+3 days')),
            'notes' => 'Follow up with patient to ensure they received care plan materials and answer any questions.',
        ]);
        echo "  ✓ Created follow-up task for care coordinator (ID: {$task['id']})\n\n";

    } catch (ApiException $e) {
        echo "  ✗ Error setting up communications: {$e->getMessage()}\n\n";
    }
}

/**
 * Step 6: Monitor Progress & Update Care Plan
 */
function monitorProgress($client, int $patientId, int $carePlanId): void
{
    echo "=================================================\n";
    echo "STEP 6: PROGRESS MONITORING (3-Month Update)\n";
    echo "=================================================\n\n";

    echo "--- Simulating 3-month progress check ---\n\n";

    try {
        // Get recent vitals
        echo "Reviewing recent vital signs...\n";
        $vitals = $client->vitals->listByPatient($patientId, [
            'since' => date('Y-m-d', strtotime('-3 months')),
        ]);

        $vitalCount = 0;
        foreach ($vitals as $vital) {
            $vitalCount++;
        }
        echo "  - {$vitalCount} vital sign recordings in past 3 months\n";

        // Check intervention adherence
        echo "\nReviewing intervention adherence...\n";
        $interventions = $client->patientInterventions->getActiveForPatient($patientId);

        foreach ($interventions as $intervention) {
            echo "  - {$intervention['title']}: ";
            // In real scenario, would check actual adherence data
            echo "85% adherence\n";
        }

        // Review lab results
        echo "\nReviewing recent lab results...\n";
        $labOrders = $client->labOrders->listByPatient($patientId, [
            'since' => date('Y-m-d', strtotime('-3 months')),
        ]);

        $labCount = 0;
        foreach ($labOrders as $order) {
            $labCount++;
        }
        echo "  - {$labCount} lab orders completed\n";

        // Update care plan based on progress
        echo "\nUpdating care plan based on progress...\n";
        $updated = $client->carePlans->updateCarePlan($carePlanId, [
            'notes' => 'Progress Update (' . date('Y-m-d') . '): Patient showing good adherence to care plan. Blood pressure improving. Continue current interventions.',
        ]);
        echo "  ✓ Care plan updated with progress notes\n";

        // Log progress discussion
        $commLog = $client->commLogs->logPhoneCall(
            patientId: $patientId,
            doctorId: $updated['doctor'],
            direction: 'Outbound',
            durationMinutes: 10,
            subject: '3-Month Progress Review',
            notes: 'Reviewed progress with patient. Discussed lab results (HbA1c decreased from 8.2% to 7.5%). Patient reports feeling better, more energy. Encouraged continued adherence.'
        );
        echo "  ✓ Documented progress discussion (Log ID: {$commLog['id']})\n\n";

    } catch (ApiException $e) {
        echo "  ✗ Error monitoring progress: {$e->getMessage()}\n\n";
    }
}

/**
 * Generate Care Coordination Report
 */
function generateCareReport($client, int $patientId, int $carePlanId): void
{
    echo "=================================================\n";
    echo "CARE COORDINATION SUMMARY REPORT\n";
    echo "=================================================\n\n";

    try {
        // Get patient info
        $patient = $client->patients->get($patientId);
        echo "Patient: {$patient['first_name']} {$patient['last_name']}\n";
        echo "MRN: {$patient['id']}\n\n";

        // Get care plan
        $carePlan = $client->carePlans->get($carePlanId);
        echo "Care Plan: {$carePlan['title']}\n";
        echo "Status: {$carePlan['status']}\n";
        echo "Duration: {$carePlan['start_date']} to {$carePlan['end_date']}\n\n";

        // Count interventions
        $interventions = $client->patientInterventions->getActiveForPatient($patientId);
        $interventionCount = 0;
        foreach ($interventions as $intervention) {
            $interventionCount++;
        }
        echo "Active Interventions: {$interventionCount}\n";

        // Count appointments
        $appointments = $client->appointments->listByPatient($patientId, [
            'since' => $carePlan['start_date'],
        ]);
        $aptCount = 0;
        $completedApts = 0;
        foreach ($appointments as $apt) {
            $aptCount++;
            if ($apt['status'] === 'Complete') {
                $completedApts++;
            }
        }
        echo "Scheduled Appointments: {$aptCount}\n";
        echo "Completed Appointments: {$completedApts}\n";

        // Count communications
        $commLogs = $client->commLogs->getPatientHistory($patientId, [
            'since' => $carePlan['start_date'],
        ]);
        $commCount = 0;
        foreach ($commLogs as $log) {
            $commCount++;
        }
        echo "Care Team Communications: {$commCount}\n\n";

        // Get risk assessment
        $riskAssessment = $client->patientRiskAssessments->getMostRecent($patientId);
        if ($riskAssessment) {
            echo "Current Risk Level: {$riskAssessment['risk_level']}\n";
        }

        echo "\n--- Report Summary ---\n";
        echo "Care coordination is progressing well. Patient is engaged with\n";
        echo "the care plan and showing improvement in key health metrics.\n";
        echo "Recommend continuing current interventions with periodic reviews.\n\n";

    } catch (ApiException $e) {
        echo "✗ Error generating report: {$e->getMessage()}\n\n";
    }
}

/**
 * Care Team Collaboration
 */
function careTeamCollaboration($client, int $patientId): void
{
    echo "=================================================\n";
    echo "CARE TEAM COLLABORATION\n";
    echo "=================================================\n\n";

    echo "Coordinating multi-disciplinary care team...\n\n";

    try {
        // Document care team meeting
        $meeting = $client->commLogs->createLog([
            'patient' => $patientId,
            'communication_type' => 'In-Person',
            'direction' => 'Internal',
            'subject' => 'Multi-disciplinary Care Team Meeting',
            'notes' => "Care team meeting attendees:\n- Primary Care Physician\n- Endocrinologist\n- Nutritionist\n- Care Coordinator\n- Pharmacist\n\nDiscussion:\n- Reviewed patient progress\n- Adjusted medication regimen\n- Intensified nutritional counseling\n- Scheduled follow-up labs\n\nAction items assigned to team members.",
            'date' => date('Y-m-d'),
        ]);
        echo "✓ Care team meeting documented (Log ID: {$meeting['id']})\n";

        // Create tasks for team members
        $tasks = [
            'Pharmacist: Review medication interactions' => 3,
            'Nutritionist: Provide meal planning guide' => 5,
            'Care Coordinator: Schedule endocrinologist consult' => 2,
            'PCP: Review updated lab results' => 7,
        ];

        echo "\nTasks assigned to care team:\n";
        foreach ($tasks as $taskTitle => $dueDays) {
            $task = $client->tasks->createTask([
                'title' => $taskTitle,
                'patient' => $patientId,
                'category' => 'Care Coordination',
                'priority' => 'High',
                'due_date' => date('Y-m-d', strtotime("+{$dueDays} days")),
            ]);
            echo "  ✓ {$taskTitle} (Due: " . date('m/d/Y', strtotime("+{$dueDays} days")) . ")\n";
        }

    } catch (ApiException $e) {
        echo "✗ Error in care team collaboration: {$e->getMessage()}\n";
    }
}

// ========================================
// Complete Care Coordination Workflow
// ========================================

function completeWorkflow($client, int $patientId, int $doctorId, int $officeId): void
{
    echo "\n";
    echo "╔═══════════════════════════════════════════════╗\n";
    echo "║   COMPREHENSIVE CARE COORDINATION WORKFLOW   ║\n";
    echo "╚═══════════════════════════════════════════════╝\n\n";

    try {
        // Step 1: Risk Assessment
        $riskAssessment = conductRiskAssessment($client, $patientId, $doctorId);

        // Step 2: Create Care Plan
        $carePlan = createCarePlan($client, $patientId, $doctorId, $riskAssessment);

        if (empty($carePlan)) {
            echo "\n✗ Cannot proceed without care plan\n";
            return;
        }

        // Step 3: Define Interventions
        $interventions = defineInterventions($client, $patientId, $doctorId, $carePlan['id']);

        // Step 4: Schedule Follow-ups
        $appointments = scheduleFollowUps($client, $patientId, $doctorId, $officeId);

        // Step 5: Setup Communication
        setupCommunicationProtocols($client, $patientId, $doctorId);

        // Step 6: Monitor Progress (simulated)
        monitorProgress($client, $patientId, $carePlan['id']);

        // Care Team Collaboration
        careTeamCollaboration($client, $patientId);

        // Generate Report
        generateCareReport($client, $patientId, $carePlan['id']);

        echo "╔═══════════════════════════════════════════════╗\n";
        echo "║         CARE COORDINATION COMPLETE            ║\n";
        echo "╚═══════════════════════════════════════════════╝\n";
        echo "✓ Comprehensive care coordination workflow completed\n";
        echo "✓ Patient enrolled in coordinated care program\n";
        echo "✓ Multi-disciplinary team assembled and coordinated\n";
        echo "✓ Follow-up appointments scheduled\n";
        echo "✓ Monitoring and communication protocols established\n\n";

    } catch (ApiException $e) {
        echo "\n✗ Workflow error: {$e->getMessage()}\n";
        echo "Status Code: {$e->getStatusCode()}\n";
    }
}

// ========================================
// Example Usage
// ========================================

if (php_sapi_name() === 'cli') {
    echo "DrChrono PHP SDK - Care Coordination Workflow Example\n\n";

    // Replace these with actual IDs from your DrChrono account
    $patientId = 123456;   // High-risk patient ID
    $doctorId = 345678;    // Primary care provider ID
    $officeId = 1;         // Office location ID

    // Run complete care coordination workflow
    completeWorkflow($client, $patientId, $doctorId, $officeId);

    echo "\n";
}

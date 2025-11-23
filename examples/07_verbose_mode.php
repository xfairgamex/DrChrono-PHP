<?php

/**
 * Example: Using Verbose Mode for Additional Data
 *
 * Demonstrates how to use verbose mode to retrieve additional fields
 * that require extra database queries.
 *
 * Verbose mode includes more detailed information but:
 * - Reduces page size from 250 to 50 records
 * - Increases response time (2-5x slower)
 * - Use only when you need the extra fields
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;

// Load access token from environment
$accessToken = getenv('DRCHRONO_ACCESS_TOKEN');
if (!$accessToken) {
    die("Error: DRCHRONO_ACCESS_TOKEN environment variable not set\n");
}

// Create client
$client = DrChronoClient::withAccessToken($accessToken);

echo "=== Verbose Mode Examples ===\n\n";

// Example 1: Patient with Insurance Details
echo "1. Getting patient with full insurance details:\n";
echo str_repeat('-', 50) . "\n";

try {
    $patientId = 789012; // Replace with actual patient ID

    // Regular mode - basic patient data only
    $patient = $client->patients->get($patientId);
    echo "Regular mode patient data:\n";
    echo "  Name: {$patient['first_name']} {$patient['last_name']}\n";
    echo "  Has primary_insurance field: " . (isset($patient['primary_insurance']) ? 'Yes' : 'No (ID only)') . "\n\n";

    // Verbose mode - includes full insurance objects
    $patientVerbose = $client->patients->getWithInsurance($patientId);
    echo "Verbose mode patient data:\n";
    echo "  Name: {$patientVerbose['first_name']} {$patientVerbose['last_name']}\n";

    if (isset($patientVerbose['primary_insurance'])) {
        $insurance = $patientVerbose['primary_insurance'];
        echo "  Primary Insurance:\n";
        echo "    Company: " . ($insurance['insurance_company'] ?? 'N/A') . "\n";
        echo "    Member ID: " . ($insurance['insurance_id_number'] ?? 'N/A') . "\n";
        echo "    Group #: " . ($insurance['insurance_group_number'] ?? 'N/A') . "\n";
        echo "    Payer ID: " . ($insurance['insurance_payer_id'] ?? 'N/A') . "\n";
    }

    if (isset($patientVerbose['custom_demographics'])) {
        echo "  Custom Demographics: Available\n";
    }

    if (isset($patientVerbose['patient_flags'])) {
        echo "  Patient Flags: " . count($patientVerbose['patient_flags']) . " flags\n";
    }

} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// Example 2: Appointment with Clinical Data
echo "2. Getting appointment with clinical details:\n";
echo str_repeat('-', 50) . "\n";

try {
    $appointmentId = 456789; // Replace with actual appointment ID

    // Regular mode
    $appointment = $client->appointments->get($appointmentId);
    echo "Regular mode appointment data:\n";
    echo "  Scheduled: {$appointment['scheduled_time']}\n";
    echo "  Duration: {$appointment['duration']} minutes\n";
    echo "  Has clinical_note: " . (isset($appointment['clinical_note']) ? 'Yes (object)' : 'No (ID only)') . "\n\n";

    // Verbose mode - includes clinical note, vitals, etc.
    $appointmentVerbose = $client->appointments->getWithClinicalData($appointmentId);
    echo "Verbose mode appointment data:\n";
    echo "  Scheduled: {$appointmentVerbose['scheduled_time']}\n";
    echo "  Duration: {$appointmentVerbose['duration']} minutes\n";

    if (isset($appointmentVerbose['clinical_note'])) {
        echo "  Clinical Note: Present (ID: {$appointmentVerbose['clinical_note']['id']})\n";
    }

    if (isset($appointmentVerbose['vitals']) && count($appointmentVerbose['vitals']) > 0) {
        echo "  Vitals recorded: " . count($appointmentVerbose['vitals']) . " measurements\n";
        foreach ($appointmentVerbose['vitals'] as $vital) {
            echo "    - {$vital['type']}: {$vital['value']}\n";
        }
    }

    if (isset($appointmentVerbose['status_transitions'])) {
        echo "  Status history: " . count($appointmentVerbose['status_transitions']) . " transitions\n";
    }

} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// Example 3: Listing with Verbose Mode
echo "3. Listing patients with insurance details:\n";
echo str_repeat('-', 50) . "\n";

try {
    // List patients with verbose mode
    $patientsVerbose = $client->patients->listWithInsurance([
        'doctor' => 123456, // Replace with actual doctor ID
        'page_size' => 10   // Note: verbose mode limits to 50 max
    ]);

    echo "Retrieved {$patientsVerbose->count()} patients (page 1)\n";
    echo "Page size limit with verbose: 50 (vs 250 regular)\n\n";

    foreach ($patientsVerbose as $patient) {
        echo "Patient: {$patient['first_name']} {$patient['last_name']}\n";

        if (isset($patient['primary_insurance']['insurance_company'])) {
            echo "  Insurance: {$patient['primary_insurance']['insurance_company']}\n";
        }

        echo "\n";
    }

} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// Example 4: Using Verbose Mode Manually
echo "4. Manual verbose mode usage:\n";
echo str_repeat('-', 50) . "\n";

try {
    // You can also pass verbose=true manually in filters
    $appointments = $client->appointments->list([
        'date' => '2025-01-15',
        'doctor' => 123456,
        'verbose' => 'true'  // Manual verbose mode
    ]);

    echo "Retrieved appointments with verbose mode\n";
    echo "Count: {$appointments->count()}\n";

} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// Best Practices
echo "=== Verbose Mode Best Practices ===\n";
echo str_repeat('-', 50) . "\n";
echo "✓ Use verbose mode only when you need the extra fields\n";
echo "✓ Cache verbose responses when possible\n";
echo "✓ Be aware of the 50-record page size limit\n";
echo "✓ Consider performance impact for large datasets\n";
echo "✓ Apply filters before enabling verbose mode\n";
echo "✗ Don't use verbose mode for bulk operations\n";
echo "✗ Don't use verbose mode if you don't need the extra data\n";

echo "\n=== Fields Requiring Verbose Mode ===\n";
echo str_repeat('-', 50) . "\n";
echo "\nPatients (/api/patients):\n";
echo "  - primary_insurance (full object)\n";
echo "  - secondary_insurance (full object)\n";
echo "  - tertiary_insurance (full object)\n";
echo "  - auto_accident_insurance\n";
echo "  - workers_comp_insurance\n";
echo "  - custom_demographics\n";
echo "  - patient_flags\n";
echo "  - referring_doctor\n";

echo "\nAppointments (/api/appointments):\n";
echo "  - clinical_note (full object)\n";
echo "  - vitals (array of measurements)\n";
echo "  - custom_vitals (array)\n";
echo "  - status_transitions (history)\n";
echo "  - reminders (settings object)\n";
echo "  - extended_updated_at\n";

echo "\nClinical Notes (/api/clinical_notes):\n";
echo "  - clinical_note_sections (detailed sections)\n";

echo "\n";

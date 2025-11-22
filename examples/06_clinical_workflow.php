<?php

/**
 * Example 6: Clinical Workflow
 *
 * Demonstrates a complete clinical workflow including:
 * - Patient check-in
 * - Recording vitals
 * - Clinical documentation
 * - Document upload
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;

$client = DrChronoClient::withAccessToken('your_access_token_here');

try {
    $appointmentId = 123456; // Replace with actual appointment ID
    $patientId = 789012;     // Replace with actual patient ID
    $doctorId = 111222;      // Replace with actual doctor ID

    // 1. Check in patient
    echo "=== Step 1: Patient Check-in ===\n";
    $appointment = $client->appointments->markArrived($appointmentId);
    echo "Patient checked in at " . date('H:i:s') . "\n";
    echo "Status: {$appointment['status']}\n";

    // 2. Record vitals
    echo "\n=== Step 2: Recording Vitals ===\n";
    $vitals = $client->vitals->createVitals([
        'patient' => $patientId,
        'appointment' => $appointmentId,
        'blood_pressure_1' => 120,
        'blood_pressure_2' => 80,
        'pulse' => 72,
        'temperature' => 98.6,
        'weight' => 165,
        'height' => 70,
    ]);
    echo "Vitals recorded:\n";
    echo "  BP: {$vitals['blood_pressure_1']}/{$vitals['blood_pressure_2']}\n";
    echo "  Pulse: {$vitals['pulse']} bpm\n";
    echo "  Temp: {$vitals['temperature']}°F\n";

    // 3. Create clinical note
    echo "\n=== Step 3: Clinical Documentation ===\n";
    $clinicalNote = $client->clinicalNotes->createNote([
        'patient' => $patientId,
        'appointment' => $appointmentId,
        'doctor' => $doctorId,
        'chief_complaint' => 'Annual physical examination',
    ]);
    echo "Clinical note created: {$clinicalNote['id']}\n";

    // 4. Update clinical note fields
    echo "\n=== Step 4: Updating Clinical Note ===\n";
    $updatedNote = $client->clinicalNotes->updateNote($clinicalNote['id'], [
        'assessment' => 'Patient presents in good health',
        'plan' => 'Continue current medications, follow up in 1 year',
    ]);
    echo "Clinical note updated\n";

    // 5. Upload document to patient chart
    echo "\n=== Step 5: Uploading Document ===\n";
    // Note: Replace with actual file path
    $documentPath = '/path/to/lab_results.pdf';
    if (file_exists($documentPath)) {
        $document = $client->documents->upload(
            doctorId: $doctorId,
            patientId: $patientId,
            filePath: $documentPath,
            description: 'Lab results from annual physical',
            date: date('Y-m-d'),
            metatags: ['Lab Results', 'Annual Physical']
        );
        echo "Document uploaded: {$document['id']}\n";
    } else {
        echo "Document file not found (skipping upload)\n";
    }

    // 6. Add problem to patient chart
    echo "\n=== Step 6: Updating Problem List ===\n";
    $problem = $client->problems->createProblem([
        'patient' => $patientId,
        'doctor' => $doctorId,
        'description' => 'Hypertension',
        'icd_code' => 'I10',
        'status' => 'active',
    ]);
    echo "Problem added to chart\n";

    // 7. Create prescription
    echo "\n=== Step 7: Creating Prescription ===\n";
    $prescription = $client->prescriptions->createPrescription([
        'patient' => $patientId,
        'doctor' => $doctorId,
        'medication' => 'Lisinopril 10mg',
        'dispense_quantity' => 30,
        'refills' => 3,
        'instructions' => 'Take one tablet by mouth daily',
    ]);
    echo "Prescription created\n";

    // 8. Complete appointment
    echo "\n=== Step 8: Completing Visit ===\n";
    $completed = $client->appointments->markComplete($appointmentId);
    echo "Appointment marked as complete\n";
    echo "Status: {$completed['status']}\n";

    // 9. Lock clinical note (finalize)
    echo "\n=== Step 9: Finalizing Clinical Note ===\n";
    $lockedNote = $client->clinicalNotes->lock($clinicalNote['id']);
    echo "Clinical note locked and finalized\n";

    echo "\n✓ Workflow completed successfully!\n";

} catch (\Exception $e) {
    echo "Error in clinical workflow: {$e->getMessage()}\n";
    if (method_exists($e, 'getErrorDetails')) {
        print_r($e->getErrorDetails());
    }
}

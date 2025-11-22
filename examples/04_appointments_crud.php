<?php

/**
 * Example 4: Appointment Management
 *
 * Demonstrates creating and managing appointments.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;
use DrChrono\Model\Appointment;

$client = DrChronoClient::withAccessToken('your_access_token_here');

try {
    // 1. List appointments for a date range
    echo "=== Listing Appointments ===\n";
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d', strtotime('+7 days'));

    $appointments = $client->appointments->listByDateRange($startDate, $endDate);
    echo "Appointments in next 7 days: {$appointments->count()}\n";

    foreach ($appointments as $apptData) {
        $appt = Appointment::fromArray($apptData);
        echo "  - {$appt->getScheduledTime()} ({$appt->getDuration()} min) - Status: {$appt->getStatus()}\n";
    }

    // 2. List appointments for specific patient
    echo "\n=== Patient Appointments ===\n";
    $patientId = 123456; // Replace with actual patient ID
    $patientAppts = $client->appointments->listByPatient($patientId);
    echo "Patient has {$patientAppts->count()} appointments\n";

    // 3. Create new appointment
    echo "\n=== Creating Appointment ===\n";
    $newAppointment = [
        'doctor' => 123456,        // Replace with actual doctor ID
        'patient' => 789012,       // Replace with actual patient ID
        'office' => 1,             // Replace with actual office ID
        'duration' => 30,          // 30 minutes
        'scheduled_time' => date('c', strtotime('+2 days 10:00')),
        'status' => 'Scheduled',
        'reason' => 'Annual checkup',
        'notes' => 'Patient requested morning appointment',
    ];

    $created = $client->appointments->createAppointment($newAppointment);
    echo "Created appointment ID: {$created['id']}\n";
    echo "Scheduled for: {$created['scheduled_time']}\n";

    // 4. Update appointment status
    echo "\n=== Updating Appointment ===\n";
    $appointmentId = $created['id'];

    // Mark as confirmed
    $updated = $client->appointments->setStatus($appointmentId, 'Confirmed');
    echo "Appointment status updated to: {$updated['status']}\n";

    // 5. Mark patient as arrived
    echo "\n=== Patient Check-in ===\n";
    $arrived = $client->appointments->markArrived($appointmentId);
    echo "Patient checked in. Status: {$arrived['status']}\n";

    // 6. Complete appointment
    echo "\n=== Complete Appointment ===\n";
    $completed = $client->appointments->markComplete($appointmentId);
    echo "Appointment completed. Status: {$completed['status']}\n";

    // 7. List appointments by doctor
    echo "\n=== Doctor's Schedule ===\n";
    $doctorId = 123456; // Replace with actual doctor ID
    $todayAppts = $client->appointments->listByDoctor($doctorId, [
        'date' => date('Y-m-d'),
    ]);

    echo "Doctor has {$todayAppts->count()} appointments today\n";

} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
    if (method_exists($e, 'getErrorDetails')) {
        print_r($e->getErrorDetails());
    }
}

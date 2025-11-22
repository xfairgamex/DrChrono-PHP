<?php

/**
 * Example 3: Patient CRUD Operations
 *
 * Demonstrates creating, reading, updating, and listing patients.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;
use DrChrono\Model\Patient;
use DrChrono\Exception\ValidationException;

$client = DrChronoClient::withAccessToken('your_access_token_here');

try {
    // 1. List patients
    echo "=== Listing Patients ===\n";
    $patients = $client->patients->list(['page_size' => 5]);

    echo "Found {$patients->count()} patients (page 1)\n";
    foreach ($patients as $patientData) {
        $patient = Patient::fromArray($patientData);
        echo "  - {$patient->getFullName()} (ID: {$patient->getId()}, DOB: {$patient->getDateOfBirth()})\n";
    }

    // 2. Search for a specific patient
    echo "\n=== Searching Patients ===\n";
    $searchResults = $client->patients->search([
        'first_name' => 'John',
        'last_name' => 'Doe'
    ]);

    echo "Found {$searchResults->count()} matching patients\n";

    // 3. Create a new patient
    echo "\n=== Creating New Patient ===\n";
    $newPatientData = [
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'gender' => 'Female',
        'date_of_birth' => '1985-03-15',
        'email' => 'jane.smith@example.com',
        'cell_phone' => '555-0123',
        'address' => '123 Main St',
        'city' => 'San Francisco',
        'state' => 'CA',
        'zip_code' => '94102',
        'doctor' => 123456, // Replace with actual doctor ID
    ];

    $createdPatient = $client->patients->createPatient($newPatientData);
    echo "Created patient with ID: {$createdPatient['id']}\n";
    echo "Chart ID: {$createdPatient['chart_id']}\n";

    // 4. Get patient details
    echo "\n=== Getting Patient Details ===\n";
    $patientDetails = $client->patients->get($createdPatient['id']);
    echo "Patient: {$patientDetails['first_name']} {$patientDetails['last_name']}\n";
    echo "Email: {$patientDetails['email']}\n";

    // 5. Update patient
    echo "\n=== Updating Patient ===\n";
    $updatedPatient = $client->patients->updateDemographics(
        $createdPatient['id'],
        ['cell_phone' => '555-9999']
    );
    echo "Updated phone to: {$updatedPatient['cell_phone']}\n";

    // 6. Iterate through all patients (using generator for memory efficiency)
    echo "\n=== Iterating All Patients ===\n";
    $count = 0;
    foreach ($client->patients->iterateAll() as $patient) {
        $count++;
        if ($count >= 10) {
            break; // Stop after 10 for demo
        }
        echo "  {$count}. {$patient['first_name']} {$patient['last_name']}\n";
    }

} catch (ValidationException $e) {
    echo "Validation error: {$e->getMessage()}\n";
    if ($e->hasValidationErrors()) {
        print_r($e->getValidationErrors());
    }
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n";
}

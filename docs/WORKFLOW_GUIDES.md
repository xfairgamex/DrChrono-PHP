# Workflow Guides

This guide provides step-by-step workflows for common healthcare operations using the DrChrono PHP SDK.

## Table of Contents

1. [Patient Registration & Scheduling](#patient-registration--scheduling)
2. [Clinical Documentation Workflow](#clinical-documentation-workflow)
3. [Billing & Claims Processing](#billing--claims-processing)
4. [Laboratory Workflow](#laboratory-workflow)
5. [Prescription Management](#prescription-management)
6. [Task Management Workflow](#task-management-workflow)
7. [Document Management](#document-management)
8. [Patient Portal Integration](#patient-portal-integration)

---

## Patient Registration & Scheduling

### Complete New Patient Workflow

This workflow covers registering a new patient and scheduling their first appointment.

```php
use DrChrono\DrChronoClient;

$client = DrChronoClient::withAccessToken($_ENV['DRCHRONO_ACCESS_TOKEN']);

// Step 1: Create the patient record
$patient = $client->patients->createPatient([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'gender' => 'Male',
    'date_of_birth' => '1985-03-15',
    'doctor' => 123,  // Primary care physician ID
    'email' => 'john.doe@example.com',
    'cell_phone' => '555-0123',
    'address' => '123 Main Street',
    'city' => 'Springfield',
    'state' => 'IL',
    'zip_code' => '62701',
    'social_security_number' => '***-**-1234',  // Last 4 only
    'ethnicity' => 'Hispanic or Latino',
    'race' => 'White',
    'preferred_language' => 'English',
]);

$patientId = $patient['id'];
echo "Created patient ID: {$patientId}\n";

// Step 2: Add insurance information
$updatedPatient = $client->patients->update($patientId, [
    'primary_insurance_company' => 'Blue Cross Blue Shield',
    'primary_insurance_plan_name' => 'PPO Gold',
    'primary_insurance_payer_id' => '12345',
    'primary_insurance_id_number' => 'ABC123456789',
    'primary_insurance_group_number' => 'GRP99999',
]);

// Step 3: Get available appointment slots
$appointments = $client->appointments->listByDateRange(
    date('Y-m-d'),
    date('Y-m-d', strtotime('+30 days')),
    [
        'doctor' => 123,
        'office' => 1,
        'status' => '',  // Empty slots
    ]
);

// Step 4: Schedule the appointment
$appointment = $client->appointments->createAppointment([
    'doctor' => 123,
    'patient' => $patientId,
    'office' => 1,
    'exam_room' => 2,
    'duration' => 30,
    'scheduled_time' => '2024-04-15T10:00:00',
    'status' => 'Scheduled',
    'reason' => 'New patient - Annual physical',
    'allow_overlapping' => false,
]);

echo "Scheduled appointment ID: {$appointment['id']} at {$appointment['scheduled_time']}\n";

// Step 5: Create a reminder task for staff
$task = $client->tasks->createTask([
    'title' => 'Send new patient welcome packet',
    'assignee' => 456,  // Staff member ID
    'patient' => $patientId,
    'due_at' => date('Y-m-d\TH:i:s', strtotime('+1 day')),
    'priority' => 'Medium',
    'notes' => 'Email intake forms and insurance verification documents',
]);

echo "Created reminder task ID: {$task['id']}\n";
```

### Patient Check-in Workflow

Handle patient arrival and vitals recording.

```php
// When patient arrives
$appointmentId = 12345;

// Step 1: Mark patient as arrived
$appointment = $client->appointments->markArrived($appointmentId);
echo "Patient marked as arrived at " . date('g:i A') . "\n";

// Step 2: Get appointment with patient details
$fullAppointment = $client->appointments->getWithClinicalData($appointmentId);
$patientId = $fullAppointment['patient'];

// Step 3: Record vitals
$vitals = $client->vitals->create([
    'patient' => $patientId,
    'appointment' => $appointmentId,
    'height' => 70,  // inches
    'weight' => 180,  // pounds
    'blood_pressure_1' => 120,  // systolic
    'blood_pressure_2' => 80,   // diastolic
    'pulse' => 72,
    'temperature' => 98.6,
    'temperature_units' => 'F',
    'oxygen_saturation' => 98,
]);

echo "Vitals recorded\n";

// Step 4: Check for any outstanding tasks
$tasks = $client->tasks->listByPatient($patientId, [
    'status' => 'Pending',
]);

foreach ($tasks as $task) {
    echo "Outstanding task: {$task['title']}\n";
}
```

---

## Clinical Documentation Workflow

### Complete Visit Documentation

Document a patient encounter from start to finish.

```php
$appointmentId = 12345;
$patientId = 456;
$doctorId = 123;

// Step 1: Get appointment with clinical data
$appointment = $client->appointments->getWithClinicalData($appointmentId);

// Step 2: Create clinical note
$note = $client->clinicalNotes->createNote([
    'appointment' => $appointmentId,
    'patient' => $patientId,
    'doctor' => $doctorId,
    'clinical_note_template' => 10,  // Template ID for office visit
]);

$noteId = $note['id'];
echo "Created clinical note ID: {$noteId}\n";

// Step 3: Document chief complaint and HPI
$client->clinicalNotes->update($noteId, [
    'chief_complaint' => 'Persistent cough for 3 weeks',
    'history_of_present_illness' => 'Patient reports dry cough, worse at night. No fever. No shortness of breath.',
]);

// Step 4: Record vital signs (if not done at check-in)
$vitals = $client->vitals->create([
    'patient' => $patientId,
    'appointment' => $appointmentId,
    'temperature' => 98.4,
    'blood_pressure_1' => 118,
    'blood_pressure_2' => 78,
    'pulse' => 68,
    'respiratory_rate' => 16,
]);

// Step 5: Document physical exam findings
$physicalExam = $client->patientPhysicalExams->create([
    'patient' => $patientId,
    'appointment' => $appointmentId,
    'doctor' => $doctorId,
    'general' => 'Alert and oriented x3, in no acute distress',
    'heent' => 'Normocephalic, atraumatic. PERRLA. TMs clear.',
    'respiratory' => 'Clear to auscultation bilaterally. No wheezes or crackles.',
    'cardiovascular' => 'Regular rate and rhythm. No murmurs.',
]);

// Step 6: Add diagnosis
$problem = $client->problems->create([
    'patient' => $patientId,
    'doctor' => $doctorId,
    'description' => 'Upper respiratory infection',
    'icd_code' => 'J06.9',
    'status' => 'active',
    'date_diagnosis' => date('Y-m-d'),
]);

// Step 7: Order lab tests if needed
$labOrder = $client->labOrders->createOrder([
    'doctor' => $doctorId,
    'patient' => $patientId,
    'appointment' => $appointmentId,
    'lab_test' => 'Chest X-ray, PA and lateral',
    'priority' => 'routine',
    'clinical_info' => 'R/O pneumonia - persistent cough',
]);

echo "Lab order created: {$labOrder['id']}\n";

// Step 8: Prescribe medication
$prescription = $client->prescriptions->createPrescription([
    'doctor' => $doctorId,
    'patient' => $patientId,
    'appointment' => $appointmentId,
    'medication' => 'Benzonatate 100mg',
    'dosage' => '1 capsule by mouth three times daily as needed for cough',
    'quantity' => '30',
    'refills' => '0',
    'notes' => 'Take with food. May cause drowsiness.',
]);

echo "Prescription created: {$prescription['id']}\n";

// Step 9: Add assessment and plan to note
$client->clinicalNotes->update($noteId, [
    'assessment' => 'Upper respiratory infection (URI)',
    'plan' => 'Benzonatate for cough suppression. Chest X-ray if no improvement in 1 week. Follow up PRN.',
]);

// Step 10: Lock/sign the note
$signedNote = $client->clinicalNotes->lock($noteId);
echo "Clinical note signed and locked\n";

// Step 11: Mark appointment as complete
$client->appointments->markComplete($appointmentId);
echo "Appointment marked complete\n";

// Step 12: Create follow-up task if needed
$followUpTask = $client->tasks->createTask([
    'title' => 'Follow up on chest X-ray results',
    'assignee' => $doctorId,
    'patient' => $patientId,
    'due_at' => date('Y-m-d\TH:i:s', strtotime('+3 days')),
    'priority' => 'Medium',
]);
```

---

## Billing & Claims Processing

### Generate Claim and Submit for Payment

Complete billing workflow from superbill to claim submission.

```php
$appointmentId = 12345;
$patientId = 456;
$doctorId = 123;

// Step 1: Get appointment details
$appointment = $client->appointments->get($appointmentId);

// Step 2: Get patient insurance information
$patient = $client->patients->getWithInsurance($patientId);

if (!isset($patient['primary_insurance'])) {
    echo "No insurance on file - will bill patient directly\n";
}

// Step 3: Create line items for services rendered
$officeVisit = $client->lineItems->create([
    'appointment' => $appointmentId,
    'doctor' => $doctorId,
    'code' => '99213',  // CPT code for office visit
    'description' => 'Office visit, established patient, level 3',
    'units' => 1,
    'adjustment' => 0,
    'allowed' => 150.00,
    'balance_total' => 150.00,
    'billed' => 150.00,
    'ins_total' => 150.00,
    'procedure_type' => 'CPT(C)',
]);

// Add additional line items if applicable
$xray = $client->lineItems->create([
    'appointment' => $appointmentId,
    'doctor' => $doctorId,
    'code' => '71046',  // CPT for chest x-ray
    'description' => 'Chest X-ray, 2 views',
    'units' => 1,
    'billed' => 75.00,
    'balance_total' => 75.00,
]);

echo "Line items created\n";

// Step 4: Check insurance eligibility
$eligibility = $client->eligibilityChecks->create([
    'appointment' => $appointmentId,
    'doctor' => $doctorId,
]);

echo "Eligibility check initiated: {$eligibility['id']}\n";

// Wait for eligibility response (may be async)
sleep(5);
$eligibilityResult = $client->eligibilityChecks->get($eligibility['id']);

if ($eligibilityResult['status'] === 'Complete') {
    echo "Patient is eligible. Coverage: {$eligibilityResult['coverage']}\n";
}

// Step 5: Create billing record
$billing = $client->billing->create([
    'appointment' => $appointmentId,
    'patient' => $patientId,
    'doctor' => $doctorId,
    'service_date' => $appointment['scheduled_time'],
    'billing_status' => 'Ready to Bill',
]);

echo "Billing record created: {$billing['id']}\n";

// Step 6: Add billing notes if needed
$billingNote = $client->claimBillingNotes->create([
    'appointment' => $appointmentId,
    'text' => 'Patient has met deductible. Standard copay applies.',
]);

// Step 7: Submit claim (if integrated with clearinghouse)
$claim = $client->billing->update($billing['id'], [
    'billing_status' => 'In Process',
    'claim_submission_date' => date('Y-m-d'),
]);

echo "Claim submitted for processing\n";

// Step 8: Record patient payment (if copay collected)
$payment = $client->patientPayments->create([
    'appointment' => $appointmentId,
    'patient' => $patientId,
    'doctor' => $doctorId,
    'amount' => 25.00,  // Copay amount
    'payment_method' => 'Credit Card',
    'received_date' => date('Y-m-d'),
    'trace_number' => 'CC' . time(),  // Transaction reference
]);

echo "Patient copay recorded: \${$payment['amount']}\n";

// Step 9: Create task for billing follow-up
$billingTask = $client->tasks->createTask([
    'title' => 'Follow up on claim status',
    'assignee' => 789,  // Billing staff ID
    'patient' => $patientId,
    'due_at' => date('Y-m-d\TH:i:s', strtotime('+14 days')),
    'notes' => "Check claim status for appointment {$appointmentId}",
]);
```

### Payment Posting & Reconciliation

Process insurance payment and post to account.

```php
$appointmentId = 12345;
$patientId = 456;

// Step 1: Get billing information
$billing = $client->billing->get($appointmentId);

// Step 2: Create insurance payment transaction
$insurancePayment = $client->transactions->create([
    'appointment' => $appointmentId,
    'type' => 'Insurance Payment',
    'amount' => 120.00,  // Payment amount from EOB
    'posted_date' => date('Y-m-d'),
    'check_number' => 'EFT987654',
    'notes' => 'Payment from BCBS - Claim #12345',
]);

echo "Insurance payment posted: \${$insurancePayment['amount']}\n";

// Step 3: Apply adjustment if needed
$adjustment = $client->transactions->create([
    'appointment' => $appointmentId,
    'type' => 'Insurance Adjustment',
    'amount' => -5.00,  // Adjustment amount (negative)
    'posted_date' => date('Y-m-d'),
    'notes' => 'Contractual adjustment per fee schedule',
]);

// Step 4: Calculate patient responsibility
$totalBilled = 150.00;
$insurancePaid = 120.00;
$patientResponsibility = $totalBilled - $insurancePaid;

// Step 5: Update billing status
$client->billing->update($billing['id'], [
    'billing_status' => 'Paid in Full',
    'insurance_paid' => $insurancePaid,
    'patient_balance' => $patientResponsibility,
]);

echo "Billing updated. Patient balance: \${$patientResponsibility}\n";

// Step 6: Send patient statement if balance due
if ($patientResponsibility > 0) {
    $message = $client->patientMessages->create([
        'patient' => $patientId,
        'doctor' => $billing['doctor'],
        'subject' => 'Statement - Balance Due',
        'body' => "Your insurance has processed your claim. Your remaining balance is \${$patientResponsibility}.",
    ]);

    echo "Statement sent to patient\n";
}
```

---

## Laboratory Workflow

### Complete Lab Order to Results Workflow

```php
$patientId = 456;
$doctorId = 123;
$appointmentId = 12345;

// Step 1: Create lab order
$labOrder = $client->labOrders->createOrder([
    'doctor' => $doctorId,
    'patient' => $patientId,
    'appointment' => $appointmentId,
    'lab_test' => 'CBC with Differential',
    'priority' => 'routine',
    'clinical_info' => 'Routine screening, patient on anticoagulation therapy',
    'icd10_codes' => ['Z00.00'],  // General exam
]);

$orderId = $labOrder['id'];
echo "Lab order created: {$orderId}\n";

// Step 2: Get printable requisition
$requisition = $client->labOrders->getOrderDocument($orderId);
echo "Requisition URL: {$requisition['document_url']}\n";

// Download requisition for patient
$pdfContent = file_get_contents($requisition['document_url']);
file_put_contents("/tmp/lab_requisition_{$orderId}.pdf", $pdfContent);

// Step 3: Create task for lab follow-up
$followUpTask = $client->tasks->createTask([
    'title' => "Review CBC results for {$labOrder['patient_name']}",
    'assignee' => $doctorId,
    'patient' => $patientId,
    'due_at' => date('Y-m-d\TH:i:s', strtotime('+5 days')),
    'notes' => "Lab order #{$orderId} - CBC with Diff",
]);

// Step 4: Wait for results (in practice, this would be automated via webhooks)
// Simulating result arrival...

// Step 5: Check for lab results
$results = $client->labResults->list([
    'patient' => $patientId,
    'lab_order' => $orderId,
]);

foreach ($results as $result) {
    echo "Result received: {$result['test_name']}\n";
    echo "Value: {$result['value']} {$result['unit']}\n";
    echo "Status: {$result['status']}\n";

    // Check for critical values
    if (isset($result['abnormal_status']) && $result['abnormal_status'] === 'Critical') {
        // Create urgent task for critical value
        $criticalTask = $client->tasks->createTask([
            'title' => "CRITICAL LAB VALUE - {$result['test_name']}",
            'assignee' => $doctorId,
            'patient' => $patientId,
            'priority' => 'High',
            'due_at' => date('Y-m-d\TH:i:s'),
            'notes' => "Critical value: {$result['value']} {$result['unit']} (Order #{$orderId})",
        ]);

        echo "CRITICAL VALUE ALERT created\n";
    }
}

// Step 6: Mark task as complete
$client->tasks->markComplete($followUpTask['id']);

// Step 7: Add result note to clinical record
$client->tasks->addNote($followUpTask['id'], 'CBC results reviewed. All values within normal limits.');

// Step 8: Send results to patient portal (if configured)
$patient = $client->patients->get($patientId);
if ($patient['allow_patient_portal']) {
    $portalMessage = $client->patientMessages->create([
        'patient' => $patientId,
        'doctor' => $doctorId,
        'subject' => 'Lab Results Available',
        'body' => 'Your recent lab results are now available in your patient portal.',
    ]);

    echo "Patient notified via portal\n";
}
```

### Lab Result Review and Follow-up

```php
$patientId = 456;
$doctorId = 123;

// Step 1: Get all pending lab results for review
$pendingResults = $client->labResults->list([
    'doctor' => $doctorId,
    'status' => 'pending',
    'since' => date('Y-m-d', strtotime('-7 days')),
]);

foreach ($pendingResults as $result) {
    echo "\nReviewing result: {$result['test_name']} for patient {$result['patient']}\n";

    // Step 2: Check if abnormal
    if (isset($result['abnormal_status']) && $result['abnormal_status'] !== 'Normal') {
        echo "Abnormal result detected: {$result['value']} {$result['unit']}\n";

        // Step 3: Create intervention if needed
        $intervention = $client->patientInterventions->create([
            'patient' => $result['patient'],
            'doctor' => $doctorId,
            'type' => 'Lab Follow-up',
            'description' => "Abnormal {$result['test_name']}: {$result['value']} {$result['unit']}",
            'date' => date('Y-m-d'),
        ]);

        // Step 4: Schedule follow-up appointment if necessary
        $followUpAppt = $client->appointments->createAppointment([
            'doctor' => $doctorId,
            'patient' => $result['patient'],
            'office' => 1,
            'duration' => 15,
            'scheduled_time' => date('Y-m-d\TH:i:s', strtotime('+1 week 09:00')),
            'status' => 'Scheduled',
            'reason' => "Follow-up on abnormal {$result['test_name']}",
        ]);

        echo "Follow-up appointment scheduled: {$followUpAppt['id']}\n";
    }

    // Step 5: Mark result as reviewed
    $client->labResults->update($result['id'], [
        'status' => 'reviewed',
        'reviewed_by' => $doctorId,
        'reviewed_at' => date('Y-m-d\TH:i:s'),
    ]);
}
```

---

## Prescription Management

### E-Prescribing Workflow

```php
$patientId = 456;
$doctorId = 123;
$appointmentId = 12345;

// Step 1: Check patient allergies before prescribing
$allergies = $client->allergies->list(['patient' => $patientId]);

foreach ($allergies as $allergy) {
    echo "Allergy: {$allergy['description']}\n";
    // Check for contraindications...
}

// Step 2: Check current medications for interactions
$currentMeds = $client->medications->list(['patient' => $patientId]);

foreach ($currentMeds as $med) {
    if ($med['status'] === 'active') {
        echo "Current medication: {$med['name']}\n";
        // Check for drug interactions...
    }
}

// Step 3: Create new prescription
$prescription = $client->prescriptions->createPrescription([
    'doctor' => $doctorId,
    'patient' => $patientId,
    'appointment' => $appointmentId,
    'medication' => 'Lisinopril 10mg',
    'dosage' => '1 tablet by mouth daily',
    'quantity' => '90',
    'refills' => '3',
    'daw' => false,  // Allow generic substitution
    'pharmacy_note' => 'May substitute generic',
    'notes' => 'For blood pressure management',
]);

echo "Prescription created: {$prescription['id']}\n";

// Step 4: Send to preferred pharmacy
$pharmacyMessage = $client->prescriptionMessages->create([
    'prescription' => $prescription['id'],
    'message_type' => 'NewRx',
]);

echo "Prescription sent to pharmacy\n";

// Step 5: Add medication to patient's med list
$medication = $client->medications->create([
    'patient' => $patientId,
    'doctor' => $doctorId,
    'name' => 'Lisinopril 10mg',
    'dosage' => '1 tablet daily',
    'status' => 'active',
    'date_started' => date('Y-m-d'),
    'indication' => 'Hypertension',
]);

// Step 6: Create patient education task
$educationTask = $client->tasks->createTask([
    'title' => 'Provide BP monitoring instructions',
    'assignee' => 789,  // Nurse ID
    'patient' => $patientId,
    'due_at' => date('Y-m-d\TH:i:s', strtotime('+1 hour')),
    'notes' => 'Patient started on Lisinopril. Educate on home BP monitoring.',
]);

// Step 7: Schedule follow-up for medication check
$followUp = $client->appointments->createAppointment([
    'doctor' => $doctorId,
    'patient' => $patientId,
    'office' => 1,
    'duration' => 15,
    'scheduled_time' => date('Y-m-d\TH:i:s', strtotime('+4 weeks 10:00')),
    'reason' => 'Blood pressure recheck',
]);

echo "Follow-up appointment scheduled in 4 weeks\n";
```

---

## Task Management Workflow

### Daily Task Management for Staff

```php
$userId = 789;  // Current staff member

// Step 1: Get today's tasks
$todaysTasks = $client->tasks->listByAssignee($userId, [
    'due_date' => date('Y-m-d'),
]);

echo "Tasks for today (" . date('F j, Y') . "):\n";

foreach ($todaysTasks as $task) {
    echo "\n[{$task['priority']}] {$task['title']}\n";
    echo "Patient: {$task['patient_name']}\n";
    echo "Due: {$task['due_at']}\n";

    // Step 2: Process each task
    switch ($task['category']) {
        case 'Patient Callback':
            // Make the call and document
            $note = $client->tasks->addNote(
                $task['id'],
                'Called patient at ' . date('g:i A') . '. Left voicemail.'
            );
            break;

        case 'Authorization':
            // Process authorization request
            $note = $client->tasks->addNote(
                $task['id'],
                'Submitted authorization request to insurance. Confirmation #12345.'
            );
            $client->tasks->markComplete($task['id']);
            break;

        case 'Prescription Refill':
            // Process refill
            // ... refill logic ...
            $client->tasks->markComplete($task['id']);
            break;
    }
}

// Step 3: Get overdue tasks
$overdueTasks = $client->tasks->listByAssignee($userId, [
    'status' => 'Pending',
    'due_before' => date('Y-m-d'),
]);

if (count($overdueTasks) > 0) {
    echo "\n⚠️  OVERDUE TASKS: " . count($overdueTasks) . "\n";
}

// Step 4: Create team task summary
$teamTasks = $client->tasks->list([
    'doctor' => 123,
    'status' => 'Pending',
]);

$summary = [
    'total' => 0,
    'high_priority' => 0,
    'due_today' => 0,
];

foreach ($teamTasks as $task) {
    $summary['total']++;

    if ($task['priority'] === 'High') {
        $summary['high_priority']++;
    }

    if (date('Y-m-d', strtotime($task['due_at'])) === date('Y-m-d')) {
        $summary['due_today']++;
    }
}

echo "\nTeam Task Summary:\n";
echo "Total pending: {$summary['total']}\n";
echo "High priority: {$summary['high_priority']}\n";
echo "Due today: {$summary['due_today']}\n";
```

---

## Document Management

### Upload and Organize Patient Documents

```php
$patientId = 456;
$doctorId = 123;

// Step 1: Upload lab results
$labResult = $client->documents->upload(
    doctorId: $doctorId,
    patientId: $patientId,
    filePath: '/path/to/lab_results.pdf',
    description: 'CBC Results - 2024-03-15',
    date: '2024-03-15',
    metatags: ['Lab Results', 'CBC', '2024']
);

echo "Lab result uploaded: {$labResult['id']}\n";

// Step 2: Upload signed consent form
$consent = $client->documents->upload(
    doctorId: $doctorId,
    patientId: $patientId,
    filePath: '/path/to/hipaa_consent.pdf',
    description: 'HIPAA Authorization - Signed',
    date: date('Y-m-d'),
    metatags: ['Consent Forms', 'HIPAA', 'Legal']
);

// Step 3: Upload insurance card images
$insuranceCardFront = $client->documents->upload(
    doctorId: $doctorId,
    patientId: $patientId,
    filePath: '/path/to/insurance_card_front.jpg',
    description: 'Insurance Card - Front',
    date: date('Y-m-d'),
    metatags: ['Insurance', 'Verification']
);

$insuranceCardBack = $client->documents->upload(
    doctorId: $doctorId,
    patientId: $patientId,
    filePath: '/path/to/insurance_card_back.jpg',
    description: 'Insurance Card - Back',
    date: date('Y-m-d'),
    metatags: ['Insurance', 'Verification']
);

// Step 4: Get all documents for patient
$allDocuments = $client->documents->listByPatient($patientId);

echo "\nPatient Documents:\n";
foreach ($allDocuments as $doc) {
    echo "- {$doc['description']} ({$doc['date']})\n";
}

// Step 5: Download specific document
$downloadUrl = $client->documents->download($labResult['id']);
$pdfContent = file_get_contents($downloadUrl);
file_put_contents('/local/path/downloaded_lab_result.pdf', $pdfContent);

echo "Document downloaded successfully\n";
```

---

## Patient Portal Integration

### Enable Patient Portal Access

```php
$patientId = 456;

// Step 1: Create patient portal access
$portalAccess = $client->patients->createOnPatientAccess($patientId);

echo "Portal access created\n";
echo "Patient login URL: {$portalAccess['access_url']}\n";
echo "Access token: {$portalAccess['access_token']}\n";

// Step 2: Send welcome email to patient
$welcomeMessage = $client->patientMessages->create([
    'patient' => $patientId,
    'doctor' => 123,
    'subject' => 'Welcome to Our Patient Portal',
    'body' => "
        Welcome to our patient portal!

        You can now:
        - View your health records
        - Request prescription refills
        - Schedule appointments
        - View test results
        - Send secure messages

        Click here to activate your account: {$portalAccess['access_url']}

        Your temporary access code: {$portalAccess['access_token']}
    ",
]);

echo "Welcome message sent\n";

// Step 3: Configure portal preferences
$client->patients->update($patientId, [
    'allow_statement_email' => true,
    'allow_appointment_reminders' => true,
    'preferred_contact_method' => 'email',
]);

// Step 4: Upload patient education materials
$educationDoc = $client->documents->upload(
    doctorId: 123,
    patientId: $patientId,
    filePath: '/path/to/hypertension_education.pdf',
    description: 'Patient Education - Managing High Blood Pressure',
    date: date('Y-m-d'),
    metatags: ['Education', 'Hypertension']
);
```

---

## Advanced Workflows

### Automated Appointment Reminders

```php
// Run daily to send appointment reminders

$tomorrow = date('Y-m-d', strtotime('+1 day'));

// Get tomorrow's appointments
$appointments = $client->appointments->listByDateRange($tomorrow, $tomorrow, [
    'status' => 'Scheduled',
]);

foreach ($appointments as $appt) {
    $patient = $client->patients->get($appt['patient']);

    // Send reminder via preferred method
    if ($patient['preferred_contact_method'] === 'email') {
        $message = $client->patientMessages->create([
            'patient' => $appt['patient'],
            'doctor' => $appt['doctor'],
            'subject' => 'Appointment Reminder',
            'body' => "
                This is a reminder of your upcoming appointment:

                Date: {$appt['scheduled_time']}
                Doctor: {$appt['doctor_name']}
                Office: {$appt['office_name']}
                Reason: {$appt['reason']}

                Please arrive 15 minutes early for check-in.
            ",
        ]);

        echo "Reminder sent to {$patient['email']}\n";
    }
}
```

### Quality Measures Tracking

```php
$doctorId = 123;

// Get patients due for annual wellness visit
$patients = $client->patients->list([
    'doctor' => $doctorId,
]);

$dueForWellness = [];

foreach ($patients as $patient) {
    // Get last wellness visit
    $appointments = $client->appointments->listByPatient($patient['id'], [
        'reason' => 'Annual physical',
        'status' => 'Complete',
    ]);

    $lastWellness = null;
    foreach ($appointments as $appt) {
        if (!$lastWellness || $appt['scheduled_time'] > $lastWellness) {
            $lastWellness = $appt['scheduled_time'];
        }
    }

    // Check if due (more than 12 months since last)
    if (!$lastWellness || strtotime($lastWellness) < strtotime('-12 months')) {
        $dueForWellness[] = $patient;

        // Create outreach task
        $task = $client->tasks->createTask([
            'title' => "Schedule annual wellness visit: {$patient['first_name']} {$patient['last_name']}",
            'assignee' => 789,  // Scheduling staff
            'patient' => $patient['id'],
            'category' => 'Outreach',
            'priority' => 'Low',
            'notes' => 'Patient due for annual physical',
        ]);
    }
}

echo "Patients due for wellness visit: " . count($dueForWellness) . "\n";
```

---

## Error Handling Best Practices

All workflows should include proper error handling:

```php
use DrChrono\Exception\ValidationException;
use DrChrono\Exception\RateLimitException;
use DrChrono\Exception\ApiException;

try {
    // Your workflow code here
    $patient = $client->patients->createPatient($patientData);

} catch (ValidationException $e) {
    // Handle validation errors
    echo "Validation failed:\n";
    foreach ($e->getValidationErrors() as $field => $errors) {
        echo "  {$field}: " . implode(', ', $errors) . "\n";
    }

    // Log for review
    error_log("Patient creation failed: " . json_encode($e->getValidationErrors()));

} catch (RateLimitException $e) {
    // Handle rate limiting
    $retryAfter = $e->getRetryAfter();
    echo "Rate limited. Retry after {$retryAfter} seconds\n";

    // Queue for retry
    // ...

} catch (ApiException $e) {
    // Handle other API errors
    echo "API Error: {$e->getMessage()}\n";
    echo "Status Code: {$e->getStatusCode()}\n";

    // Log and alert
    error_log("API Error: " . $e->getMessage());
}
```

---

## See Also

- [Verbose Mode Guide](VERBOSE_MODE.md) - Detailed guide on using verbose mode
- [Best Practices](BEST_PRACTICES.md) - General SDK best practices
- [API Coverage Audit](API_COVERAGE_AUDIT.md) - Complete endpoint coverage
- [Laravel Integration](LARAVEL_INTEGRATION.md) - Using the SDK with Laravel

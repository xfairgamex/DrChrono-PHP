<?php

/**
 * Complete Billing Workflow Example
 *
 * This example demonstrates a complete revenue cycle from appointment
 * to payment collection, including:
 * - Insurance eligibility verification
 * - Fee schedule lookup
 * - Line item creation
 * - Transaction recording
 * - Payment reconciliation
 *
 * Use case: Medical practice wants to automate billing workflow
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;
use DrChrono\Exception\ApiException;
use DrChrono\Exception\ValidationException;

// Initialize client
$client = DrChronoClient::withAccessToken($_ENV['DRCHRONO_ACCESS_TOKEN'] ?? 'your_access_token');

/**
 * Step 1: Pre-Appointment - Verify Insurance Eligibility
 */
function verifyInsuranceEligibility($client, int $patientId, int $appointmentId): array
{
    echo "Step 1: Verifying insurance eligibility...\n";

    try {
        // Check primary insurance eligibility
        $eligibility = $client->eligibilityChecks->verifyPrimaryInsurance([
            'patient' => $patientId,
            'appointment' => $appointmentId,
        ]);

        if ($eligibility['is_eligible']) {
            echo "✓ Patient is eligible\n";
            echo "  - Copay Amount: \${$eligibility['copay_amount']}\n";
            echo "  - Deductible Met: " . ($eligibility['deductible_met'] ? 'Yes' : 'No') . "\n";
            echo "  - Coverage Level: {$eligibility['coverage_level']}%\n";

            return [
                'eligible' => true,
                'copay' => $eligibility['copay_amount'],
                'coverage' => $eligibility['coverage_level'],
            ];
        } else {
            echo "✗ Eligibility check failed: {$eligibility['error_message']}\n";
            return ['eligible' => false, 'error' => $eligibility['error_message']];
        }
    } catch (ApiException $e) {
        echo "✗ Eligibility check error: {$e->getMessage()}\n";
        return ['eligible' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Step 2: During Appointment - Document Services
 */
function documentServices($client, int $appointmentId, int $doctorId): array
{
    echo "\nStep 2: Documenting services provided...\n";

    // Example services provided during visit
    $services = [
        ['code' => '99214', 'description' => 'Office Visit - Established Patient', 'units' => 1],
        ['code' => '80053', 'description' => 'Comprehensive Metabolic Panel', 'units' => 1],
        ['code' => '85025', 'description' => 'Complete Blood Count', 'units' => 1],
    ];

    $documentedServices = [];

    foreach ($services as $service) {
        echo "  - {$service['description']} ({$service['code']})\n";
        $documentedServices[] = $service;
    }

    return $documentedServices;
}

/**
 * Step 3: Get Pricing from Fee Schedule
 */
function getPricing($client, array $services, int $doctorId, ?int $insurancePlanId = null): array
{
    echo "\nStep 3: Looking up prices from fee schedule...\n";

    $pricedServices = [];

    foreach ($services as $service) {
        try {
            // Get fee for this service
            $feeSchedule = $client->feeSchedules->getByCode($service['code'], [
                'doctor' => $doctorId,
                'insurance_plan' => $insurancePlanId,
            ]);

            if ($feeSchedule) {
                $price = $feeSchedule['price'] ?? 0;
                $totalCharge = $price * $service['units'];

                echo "  - {$service['code']}: \${$price} x {$service['units']} = \${$totalCharge}\n";

                $pricedServices[] = array_merge($service, [
                    'price' => $price,
                    'total_charge' => $totalCharge,
                    'fee_schedule_id' => $feeSchedule['id'],
                ]);
            } else {
                // Use default pricing if no fee schedule found
                echo "  - {$service['code']}: Using standard rate\n";
                $pricedServices[] = array_merge($service, [
                    'price' => 100.00,
                    'total_charge' => 100.00,
                ]);
            }
        } catch (ApiException $e) {
            echo "  ! Could not find fee schedule for {$service['code']}, using default\n";
            $pricedServices[] = array_merge($service, [
                'price' => 100.00,
                'total_charge' => 100.00,
            ]);
        }
    }

    $totalCharges = array_sum(array_column($pricedServices, 'total_charge'));
    echo "  Total Charges: \${$totalCharges}\n";

    return [
        'services' => $pricedServices,
        'total' => $totalCharges,
    ];
}

/**
 * Step 4: Create Line Items for Billing
 */
function createLineItems($client, int $appointmentId, int $doctorId, array $services): array
{
    echo "\nStep 4: Creating line items for claim...\n";

    $lineItems = [];

    foreach ($services as $service) {
        try {
            $lineItem = $client->lineItems->addProcedure($appointmentId, [
                'code' => $service['code'],
                'units' => $service['units'],
                'price' => $service['price'],
                'doctor' => $doctorId,
                'procedure_type' => 'CPT',
            ]);

            echo "  ✓ Line item created: {$service['description']} (ID: {$lineItem['id']})\n";
            $lineItems[] = $lineItem;
        } catch (ValidationException $e) {
            echo "  ✗ Failed to create line item for {$service['code']}: ";
            print_r($e->getValidationErrors());
        }
    }

    return $lineItems;
}

/**
 * Step 5: Collect Copay Payment
 */
function collectCopay($client, int $patientId, int $appointmentId, float $copayAmount, int $doctorId): ?array
{
    echo "\nStep 5: Collecting copay payment...\n";

    if ($copayAmount <= 0) {
        echo "  No copay required\n";
        return null;
    }

    try {
        // Record copay payment
        $payment = $client->transactions->recordPayment($appointmentId, [
            'amount' => $copayAmount,
            'payment_method' => 'Credit Card',
            'posted_date' => date('Y-m-d'),
            'doctor' => $doctorId,
            'appointment' => $appointmentId,
        ]);

        echo "  ✓ Copay collected: \${$copayAmount} (Transaction ID: {$payment['id']})\n";

        // Also record in patient payments
        $patientPayment = $client->patientPayments->createPayment([
            'patient' => $patientId,
            'appointment' => $appointmentId,
            'amount' => $copayAmount,
            'payment_method' => 'Credit Card',
            'payment_date' => date('Y-m-d'),
        ]);

        return $payment;
    } catch (ApiException $e) {
        echo "  ✗ Failed to record copay: {$e->getMessage()}\n";
        return null;
    }
}

/**
 * Step 6: Submit Claim to Insurance
 */
function submitInsuranceClaim($client, int $appointmentId, array $lineItems): void
{
    echo "\nStep 6: Submitting claim to insurance...\n";

    // In real workflow, this would submit claim electronically
    // For this example, we'll just update the billing status

    echo "  ✓ Claim submitted with " . count($lineItems) . " line items\n";
    echo "  - Claim will be processed by insurance payer\n";
    echo "  - Expected reimbursement: " . array_sum(array_column($lineItems, 'price')) . "\n";
}

/**
 * Step 7: Record Insurance Payment (when received)
 */
function recordInsurancePayment($client, int $appointmentId, float $amount, int $doctorId, string $checkNumber = ''): array
{
    echo "\nStep 7: Recording insurance payment...\n";

    try {
        $payment = $client->transactions->create([
            'appointment' => $appointmentId,
            'amount' => $amount,
            'transaction_type' => 'Payment',
            'posted_date' => date('Y-m-d'),
            'ins_name' => 'Primary Insurance',
            'check_number' => $checkNumber,
            'doctor' => $doctorId,
        ]);

        echo "  ✓ Insurance payment recorded: \${$amount}\n";

        if ($checkNumber) {
            echo "  - Check #: {$checkNumber}\n";
        }

        return $payment;
    } catch (ApiException $e) {
        echo "  ✗ Failed to record insurance payment: {$e->getMessage()}\n";
        throw $e;
    }
}

/**
 * Step 8: Calculate Patient Balance
 */
function calculatePatientBalance($client, int $patientId, int $appointmentId): array
{
    echo "\nStep 8: Calculating patient balance...\n";

    // Get all transactions for this appointment
    $transactions = $client->transactions->listByAppointment($appointmentId);

    $totalCharges = 0;
    $totalPayments = 0;
    $totalAdjustments = 0;

    foreach ($transactions as $transaction) {
        if ($transaction['transaction_type'] === 'Payment') {
            $totalPayments += abs($transaction['amount']);
        } elseif ($transaction['transaction_type'] === 'Adjustment') {
            $totalAdjustments += abs($transaction['amount']);
        }
    }

    // Get total charges from line items
    $lineItems = $client->lineItems->listByAppointment($appointmentId);
    foreach ($lineItems as $item) {
        $totalCharges += $item['price'] * $item['units'];
    }

    $patientBalance = $totalCharges - $totalPayments - $totalAdjustments;

    echo "  - Total Charges: \${$totalCharges}\n";
    echo "  - Total Payments: \${$totalPayments}\n";
    echo "  - Total Adjustments: \${$totalAdjustments}\n";
    echo "  - Patient Balance: \${$patientBalance}\n";

    return [
        'charges' => $totalCharges,
        'payments' => $totalPayments,
        'adjustments' => $totalAdjustments,
        'balance' => $patientBalance,
    ];
}

/**
 * Step 9: Send Patient Statement (if balance due)
 */
function sendPatientStatement($client, int $patientId, float $balance): void
{
    echo "\nStep 9: Patient statement...\n";

    if ($balance <= 0) {
        echo "  ✓ No balance due - account settled\n";
        return;
    }

    echo "  → Patient statement will be sent for balance: \${$balance}\n";

    // Create task for billing team to follow up
    try {
        $task = $client->tasks->createTask([
            'title' => 'Follow up on patient balance',
            'patient' => $patientId,
            'due_date' => date('Y-m-d', strtotime('+30 days')),
            'category' => 'Billing',
            'status' => 'Open',
            'notes' => "Patient balance: \${$balance}. Send statement and follow up for payment.",
        ]);

        echo "  ✓ Follow-up task created (ID: {$task['id']})\n";
    } catch (ApiException $e) {
        echo "  ! Could not create follow-up task: {$e->getMessage()}\n";
    }
}

/**
 * Complete Billing Workflow
 */
function completeBillingWorkflow($client, int $patientId, int $appointmentId, int $doctorId): void
{
    echo "=================================================\n";
    echo "COMPLETE BILLING WORKFLOW\n";
    echo "=================================================\n\n";
    echo "Patient ID: {$patientId}\n";
    echo "Appointment ID: {$appointmentId}\n";
    echo "Doctor ID: {$doctorId}\n\n";

    try {
        // Step 1: Verify eligibility
        $eligibility = verifyInsuranceEligibility($client, $patientId, $appointmentId);

        if (!$eligibility['eligible']) {
            echo "\n✗ Cannot proceed - patient not eligible\n";
            return;
        }

        // Step 2: Document services
        $services = documentServices($client, $appointmentId, $doctorId);

        // Step 3: Get pricing
        $pricing = getPricing($client, $services, $doctorId);

        // Step 4: Create line items
        $lineItems = createLineItems($client, $appointmentId, $doctorId, $pricing['services']);

        // Step 5: Collect copay
        $copayPayment = collectCopay(
            $client,
            $patientId,
            $appointmentId,
            $eligibility['copay'] ?? 0,
            $doctorId
        );

        // Step 6: Submit to insurance
        submitInsuranceClaim($client, $appointmentId, $lineItems);

        // Simulate insurance payment (in real workflow, this happens days/weeks later)
        echo "\n--- Simulating insurance payment (received 2 weeks later) ---\n";

        // Step 7: Record insurance payment (example: insurance paid 80%)
        $insurancePayment = $pricing['total'] * 0.80;
        recordInsurancePayment($client, $appointmentId, $insurancePayment, $doctorId, 'INS123456');

        // Step 8: Calculate remaining balance
        $balance = calculatePatientBalance($client, $patientId, $appointmentId);

        // Step 9: Send statement if needed
        sendPatientStatement($client, $patientId, $balance['balance']);

        echo "\n=================================================\n";
        echo "BILLING WORKFLOW COMPLETE\n";
        echo "=================================================\n";
        echo "✓ All billing steps completed successfully\n";
        echo "Final patient balance: \${$balance['balance']}\n";

    } catch (ApiException $e) {
        echo "\n✗ Workflow error: {$e->getMessage()}\n";
        echo "Status Code: {$e->getStatusCode()}\n";
    }
}

// ========================================
// Example Usage
// ========================================

if (php_sapi_name() === 'cli') {
    echo "DrChrono PHP SDK - Complete Billing Workflow Example\n\n";

    // Replace these with actual IDs from your DrChrono account
    $patientId = 123456;      // Your patient ID
    $appointmentId = 789012;  // Your appointment ID
    $doctorId = 345678;       // Your doctor ID

    // Run complete workflow
    completeBillingWorkflow($client, $patientId, $appointmentId, $doctorId);

    echo "\n";
}

/**
 * Additional Examples
 */

// Example: Batch process appointments for billing
function batchProcessAppointments($client, array $appointmentIds, int $doctorId): void
{
    echo "\nBatch processing " . count($appointmentIds) . " appointments for billing...\n";

    foreach ($appointmentIds as $appointmentId) {
        try {
            // Get appointment details
            $appointment = $client->appointments->get($appointmentId);
            $patientId = $appointment['patient'];

            echo "\nProcessing appointment #{$appointmentId} for patient #{$patientId}...\n";

            // Run billing workflow for each appointment
            completeBillingWorkflow($client, $patientId, $appointmentId, $doctorId);

        } catch (ApiException $e) {
            echo "  ✗ Failed to process appointment #{$appointmentId}: {$e->getMessage()}\n";
            continue;
        }
    }
}

// Example: Generate billing report
function generateBillingReport($client, int $doctorId, string $startDate, string $endDate): array
{
    echo "\nGenerating billing report from {$startDate} to {$endDate}...\n";

    // Get all transactions in date range
    $transactions = $client->transactions->listByDoctor($doctorId, [
        'since' => $startDate,
        'date' => $endDate,
    ]);

    $totalCharges = 0;
    $totalPayments = 0;
    $totalAdjustments = 0;

    foreach ($transactions as $transaction) {
        if ($transaction['transaction_type'] === 'Payment') {
            $totalPayments += abs($transaction['amount']);
        } elseif ($transaction['transaction_type'] === 'Adjustment') {
            $totalAdjustments += abs($transaction['amount']);
        }
    }

    // Get all line items
    $lineItems = $client->lineItems->listByDoctor($doctorId, [
        'since' => $startDate,
    ]);

    foreach ($lineItems as $item) {
        $totalCharges += $item['price'] * $item['units'];
    }

    $report = [
        'period' => "{$startDate} to {$endDate}",
        'total_charges' => $totalCharges,
        'total_payments' => $totalPayments,
        'total_adjustments' => $totalAdjustments,
        'outstanding_balance' => $totalCharges - $totalPayments - $totalAdjustments,
        'collection_rate' => $totalCharges > 0 ? ($totalPayments / $totalCharges) * 100 : 0,
    ];

    echo "\n--- Billing Report ---\n";
    echo "Period: {$report['period']}\n";
    echo "Total Charges: \${$report['total_charges']}\n";
    echo "Total Payments: \${$report['total_payments']}\n";
    echo "Total Adjustments: \${$report['total_adjustments']}\n";
    echo "Outstanding Balance: \${$report['outstanding_balance']}\n";
    echo "Collection Rate: " . round($report['collection_rate'], 2) . "%\n";

    return $report;
}

// Example: Automated payment posting
function autoPostInsurancePayments($client, int $doctorId): void
{
    echo "\nAuto-posting insurance payments...\n";

    // Get all transactions that need posting
    $transactions = $client->transactions->listByDoctor($doctorId, [
        'status' => 'pending',
    ]);

    foreach ($transactions as $transaction) {
        // Verify and post payment
        if ($transaction['transaction_type'] === 'Payment' && $transaction['amount'] > 0) {
            echo "  → Posting payment of \${$transaction['amount']} for appointment #{$transaction['appointment']}\n";

            // Update transaction status (pseudo-code - actual implementation depends on your workflow)
            // $client->transactions->update($transaction['id'], ['status' => 'posted']);
        }
    }
}

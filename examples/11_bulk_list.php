<?php

/**
 * Example 11: Bulk List Endpoints
 *
 * DrChrono bulk endpoints use an async POST-then-poll pattern that supports
 * page sizes up to 1000 (vs. 250 for standard endpoints). Results are cached
 * server-side for 1 hour.
 *
 * Supported resources: appointments, patients, lineItems, transactions,
 * prescriptionMessages, eligibilityChecks, clinicalNotes, clinicalNoteFieldValues
 *
 * @see https://support.drchrono.com/home/api-bulk-endpoints
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;
use DrChrono\Exception\ApiException;
use DrChrono\Exception\BulkTimeoutException;

$client = DrChronoClient::withAccessToken('your_access_token_here');

// ─── Basic: Fetch a single page of bulk results ─────────────────────────

try {
    $bulk = $client->appointments->bulkList([
        'since' => '2024-01-01',
        'verbose' => true,
    ]);

    echo "Status: {$bulk->getStatus()}\n";
    echo "UUID: {$bulk->getUuid()}\n";
    echo "Page {$bulk->getCurrentPage()} of {$bulk->getTotalPages()}\n";
    echo "Total records: {$bulk->getTotalCount()}\n";
    echo "Results on this page: " . count($bulk->getResults()) . "\n\n";

    foreach ($bulk->getResults() as $appointment) {
        echo "  Appointment {$appointment['id']}: {$appointment['scheduled_time']}\n";
    }
} catch (BulkTimeoutException $e) {
    // Polling timed out — you can resume manually with the UUID
    echo "Timed out. UUID: {$e->getUuid()}\n";
    echo "Path: {$e->getBulkListPath()}\n";
} catch (ApiException $e) {
    echo "API error: {$e->getMessage()}\n";
}

// ─── Iterate all pages automatically (memory-efficient) ──────────────────

echo "\n--- Iterating all patients ---\n";

try {
    $count = 0;
    foreach ($client->patients->bulkIterateAll(['since' => '2024-06-01']) as $patient) {
        $count++;
        echo "  Patient {$patient['id']}: {$patient['first_name']} {$patient['last_name']}\n";

        // Break early if needed
        if ($count >= 100) {
            echo "  (stopped after 100 for demo)\n";
            break;
        }
    }
    echo "Processed {$count} patients\n";
} catch (BulkTimeoutException $e) {
    echo "Timed out on a page. UUID: {$e->getUuid()}\n";
}

// ─── Get all results at once (caution: uses more memory) ─────────────────

echo "\n--- Fetching all transactions ---\n";

try {
    $allTransactions = $client->transactions->bulkAll(['since' => '2024-01-01']);
    echo "Total transactions loaded: " . count($allTransactions) . "\n";
} catch (BulkTimeoutException $e) {
    echo "Timed out. UUID: {$e->getUuid()}\n";
}

// ─── Custom page size and specific page ──────────────────────────────────

echo "\n--- Custom page size ---\n";

$bulk = $client->lineItems->bulkList(
    ['since' => '2024-01-01'],
    500,  // page_size (max 1000)
    2     // page number
);
echo "Page {$bulk->getCurrentPage()} of {$bulk->getTotalPages()} (page_size=500)\n";

// ─── Configure polling behavior ──────────────────────────────────────────

echo "\n--- Custom polling config ---\n";

$client = DrChronoClient::withAccessToken('your_access_token_here', [
    'bulk_poll_interval' => 10,      // seconds between polls (default: 5)
    'bulk_poll_max_attempts' => 120,  // max attempts (default: 60)
]);

// These settings apply to all bulkList/bulkIterateAll/bulkAll calls
$bulk = $client->clinicalNotes->bulkList(['since' => '2024-01-01']);

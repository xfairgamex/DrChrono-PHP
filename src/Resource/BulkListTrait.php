<?php

declare(strict_types=1);

namespace DrChrono\Resource;

use Generator;

/**
 * Trait providing bulk list operations for resources that support the async bulk API
 *
 * Supported resources: Appointments, Patients, LineItems, Transactions,
 * PrescriptionMessages, EligibilityChecks, ClinicalNotes, ClinicalNoteFieldValues
 *
 * Bulk endpoints allow page sizes up to 1000 (vs. 250 standard) using an async
 * POST-then-poll pattern. Each page requires a separate POST request.
 *
 * @see https://support.drchrono.com/home/api-bulk-endpoints
 */
trait BulkListTrait
{
    /**
     * Create a bulk list operation for a single page
     *
     * Submits a POST request and polls until results are available.
     *
     * @param array $filters Query parameters (since, verbose, order_by, doctor, patient, etc.)
     * @param int $pageSize Results per page (max 1000, default 1000)
     * @param int $page Page number (default 1)
     * @return BulkOperation Completed bulk operation with results
     *
     * @example
     * $bulk = $client->appointments->bulkList(['since' => '2024-01-01', 'verbose' => true]);
     * foreach ($bulk->getResults() as $appointment) {
     *     echo $appointment['id'] . "\n";
     * }
     * echo "Total: {$bulk->getTotalCount()}, Page {$bulk->getCurrentPage()} of {$bulk->getTotalPages()}\n";
     */
    public function bulkList(array $filters = [], int $pageSize = 1000, int $page = 1): BulkOperation
    {
        $filters['page_size'] = $pageSize;
        $filters['page'] = $page;

        $operation = new BulkOperation(
            $this->httpClient,
            $this->getBulkCreatePath(),
            $this->getBulkListPath()
        );

        return $operation->submitAndPoll($filters);
    }

    /**
     * Iterate through all pages of bulk results automatically
     *
     * Yields individual items across all pages. Each page requires a fresh
     * POST + poll cycle. This is memory-efficient for very large datasets.
     *
     * @param array $filters Query parameters
     * @param int $pageSize Results per page (max 1000, default 1000)
     * @return Generator Yields individual result items
     *
     * @example
     * foreach ($client->patients->bulkIterateAll(['since' => '2024-01-01']) as $patient) {
     *     processPatient($patient);
     * }
     */
    public function bulkIterateAll(array $filters = [], int $pageSize = 1000): Generator
    {
        $page = 1;

        do {
            $operation = $this->bulkList($filters, $pageSize, $page);

            foreach ($operation->getResults() as $item) {
                yield $item;
            }

            $page++;
        } while ($page <= $operation->getTotalPages());
    }

    /**
     * Get all bulk results across all pages (caution: memory intensive for large datasets)
     *
     * @param array $filters Query parameters
     * @param int $pageSize Results per page (max 1000, default 1000)
     * @return array All results combined
     */
    public function bulkAll(array $filters = [], int $pageSize = 1000): array
    {
        return iterator_to_array($this->bulkIterateAll($filters, $pageSize), false);
    }

    /**
     * Get the POST endpoint path for creating a bulk list operation
     *
     * Maps /api/appointments → /api/appointments_list_create
     */
    protected function getBulkCreatePath(): string
    {
        return $this->resourcePath . '_list_create';
    }

    /**
     * Get the GET endpoint path for polling bulk list results
     *
     * Maps /api/appointments → /api/appointments_list_list
     */
    protected function getBulkListPath(): string
    {
        return $this->resourcePath . '_list_list';
    }
}

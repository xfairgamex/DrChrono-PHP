<?php

declare(strict_types=1);

namespace DrChrono\Exception;

/**
 * Exception thrown when a bulk operation polling exceeds the maximum attempts
 *
 * The UUID is preserved so callers can resume polling manually if desired.
 */
class BulkTimeoutException extends DrChronoException
{
    private string $uuid;
    private string $bulkListPath;

    public function __construct(string $uuid, string $bulkListPath, int $maxAttempts)
    {
        $this->uuid = $uuid;
        $this->bulkListPath = $bulkListPath;

        parent::__construct(
            "Bulk operation timed out after {$maxAttempts} polling attempts. UUID: {$uuid}"
        );
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getBulkListPath(): string
    {
        return $this->bulkListPath;
    }
}

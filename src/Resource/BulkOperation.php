<?php

declare(strict_types=1);

namespace DrChrono\Resource;

use DrChrono\Client\HttpClient;
use DrChrono\Exception\ApiException;
use DrChrono\Exception\BulkTimeoutException;

/**
 * Represents a single bulk list operation using the async POST-then-poll pattern
 *
 * DrChrono bulk endpoints allow page sizes up to 1000 (vs. 250 standard) by:
 * 1. POSTing to /{resource}_list_create with filters → returns a UUID
 * 2. GETting /{resource}_list_list?uuid={uuid} → polls until status is "Complete"
 *
 * Results are cached server-side for 1 hour. After expiration, the UUID becomes invalid
 * and a new POST must be submitted.
 */
class BulkOperation
{
    private HttpClient $httpClient;
    private string $createPath;
    private string $listPath;
    private ?string $uuid = null;
    private string $status = '';
    private array $pagination = [];
    private array $results = [];

    public function __construct(HttpClient $httpClient, string $createPath, string $listPath)
    {
        $this->httpClient = $httpClient;
        $this->createPath = $createPath;
        $this->listPath = $listPath;
    }

    /**
     * Submit a bulk list request
     *
     * POSTs to the _list_create endpoint with filters as query parameters.
     * The API returns a UUID to poll for results.
     *
     * @param array $filters Query parameters (since, page, page_size, verbose, order_by, etc.)
     * @return $this
     */
    public function submit(array $filters = []): self
    {
        $response = $this->httpClient->postWithQuery($this->createPath, $filters);

        $this->uuid = $response['uuid'] ?? null;
        $this->status = $response['status'] ?? '';

        if ($this->uuid === null) {
            throw new ApiException(
                'Bulk operation POST did not return a UUID',
                0,
                $response,
                'bulk_error'
            );
        }

        return $this;
    }

    /**
     * Poll the bulk list endpoint until the operation completes
     *
     * @param int $intervalSeconds Seconds between poll attempts
     * @param int $maxAttempts Maximum number of poll attempts before timing out
     * @return $this
     * @throws BulkTimeoutException When max attempts are exceeded
     * @throws ApiException When the UUID is expired or invalid
     */
    public function poll(int $intervalSeconds = 5, int $maxAttempts = 60): self
    {
        if ($this->uuid === null) {
            throw new ApiException(
                'No UUID available. Call submit() first.',
                0,
                [],
                'bulk_error'
            );
        }

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $response = $this->httpClient->get($this->listPath, ['uuid' => $this->uuid]);

            // Check for expired/invalid UUID
            if (isset($response['detail']) && str_contains(strtolower($response['detail']), 'invalid uuid')) {
                throw new ApiException(
                    "Bulk operation UUID has expired: {$this->uuid}",
                    0,
                    $response,
                    'bulk_expired'
                );
            }

            $this->status = $response['status'] ?? '';

            if ($this->isComplete()) {
                $this->pagination = $response['pagination'] ?? [];
                $this->results = $response['results'] ?? [];
                return $this;
            }

            sleep($intervalSeconds);
        }

        throw new BulkTimeoutException($this->uuid, $this->listPath, $maxAttempts);
    }

    /**
     * Submit and poll in one call using config defaults
     *
     * @param array $filters Query parameters
     * @return $this
     */
    public function submitAndPoll(array $filters = []): self
    {
        $config = $this->httpClient->getConfig();
        return $this->submit($filters)->poll(
            $config->getBulkPollInterval(),
            $config->getBulkPollMaxAttempts()
        );
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isComplete(): bool
    {
        return $this->status === 'Complete';
    }

    /**
     * @return array{count: int, pages: int, page_size: int, page: int}
     */
    public function getPagination(): array
    {
        return $this->pagination;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function getTotalCount(): int
    {
        return (int) ($this->pagination['count'] ?? 0);
    }

    public function getTotalPages(): int
    {
        return (int) ($this->pagination['pages'] ?? 0);
    }

    public function getCurrentPage(): int
    {
        return (int) ($this->pagination['page'] ?? 0);
    }

    public function getPageSize(): int
    {
        return (int) ($this->pagination['page_size'] ?? 0);
    }
}

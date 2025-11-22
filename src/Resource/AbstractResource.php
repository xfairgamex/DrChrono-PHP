<?php

declare(strict_types=1);

namespace DrChrono\Resource;

use DrChrono\Client\HttpClient;
use Generator;

/**
 * Base class for all API resource classes
 */
abstract class AbstractResource
{
    protected HttpClient $httpClient;
    protected string $resourcePath;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * List resources with optional filters
     */
    public function list(array $filters = []): PagedCollection
    {
        $response = $this->httpClient->get($this->resourcePath, $filters);
        return PagedCollection::fromApiResponse($response);
    }

    /**
     * Get a single resource by ID
     */
    public function get(int|string $id): array
    {
        return $this->httpClient->get("{$this->resourcePath}/{$id}");
    }

    /**
     * Create a new resource
     */
    public function create(array $data): array
    {
        return $this->httpClient->post($this->resourcePath, $data);
    }

    /**
     * Update an existing resource
     */
    public function update(int|string $id, array $data): array
    {
        return $this->httpClient->patch("{$this->resourcePath}/{$id}", $data);
    }

    /**
     * Delete a resource
     */
    public function delete(int|string $id): void
    {
        $this->httpClient->delete("{$this->resourcePath}/{$id}");
    }

    /**
     * Iterate through all pages automatically
     *
     * @return Generator
     */
    public function iterateAll(array $filters = []): Generator
    {
        $nextUrl = null;

        do {
            if ($nextUrl) {
                // Extract query parameters from next URL
                $urlParts = parse_url($nextUrl);
                parse_str($urlParts['query'] ?? '', $params);
                $response = $this->httpClient->get($this->resourcePath, $params);
            } else {
                $response = $this->httpClient->get($this->resourcePath, $filters);
            }

            $collection = PagedCollection::fromApiResponse($response);

            foreach ($collection->getItems() as $item) {
                yield $item;
            }

            $nextUrl = $collection->getNextUrl();
        } while ($nextUrl !== null);
    }

    /**
     * Get all items (caution: may be memory intensive for large datasets)
     */
    public function all(array $filters = []): array
    {
        return iterator_to_array($this->iterateAll($filters), false);
    }

    protected function buildPath(string $path, array $params = []): string
    {
        if (empty($params)) {
            return $path;
        }

        return $path . '?' . http_build_query($params);
    }
}

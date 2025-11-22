<?php

declare(strict_types=1);

namespace DrChrono\Client;

use DrChrono\Exception\ApiException;
use DrChrono\Exception\AuthenticationException;
use DrChrono\Exception\RateLimitException;
use DrChrono\Exception\ValidationException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP client wrapper for DrChrono API
 */
class HttpClient
{
    private Client $httpClient;
    private Config $config;
    private int $requestCount = 0;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->httpClient = new Client([
            'base_uri' => $config->getBaseUri(),
            'timeout' => $config->getTimeout(),
            'connect_timeout' => $config->getConnectTimeout(),
            'headers' => [
                'User-Agent' => $config->getUserAgent(),
                'Accept' => 'application/json',
            ],
        ]);
    }

    public function get(string $path, array $query = [], array $headers = []): array
    {
        return $this->request('GET', $path, [
            'query' => $query,
            'headers' => $headers,
        ]);
    }

    public function post(string $path, array $data = [], array $headers = []): array
    {
        return $this->request('POST', $path, [
            'json' => $data,
            'headers' => $headers,
        ]);
    }

    public function put(string $path, array $data = [], array $headers = []): array
    {
        return $this->request('PUT', $path, [
            'json' => $data,
            'headers' => $headers,
        ]);
    }

    public function patch(string $path, array $data = [], array $headers = []): array
    {
        return $this->request('PATCH', $path, [
            'json' => $data,
            'headers' => $headers,
        ]);
    }

    public function delete(string $path, array $headers = []): array
    {
        return $this->request('DELETE', $path, [
            'headers' => $headers,
        ]);
    }

    public function upload(string $path, array $data = [], array $files = []): array
    {
        $multipart = [];

        foreach ($data as $name => $contents) {
            $multipart[] = [
                'name' => $name,
                'contents' => is_array($contents) ? json_encode($contents) : $contents,
            ];
        }

        foreach ($files as $name => $file) {
            $multipart[] = [
                'name' => $name,
                'contents' => fopen($file, 'r'),
                'filename' => basename($file),
            ];
        }

        return $this->request('POST', $path, [
            'multipart' => $multipart,
        ]);
    }

    private function request(string $method, string $path, array $options = [], int $retryCount = 0): array
    {
        $options['headers'] = array_merge(
            $options['headers'] ?? [],
            $this->getAuthHeaders()
        );

        if ($this->config->getApiVersion()) {
            $options['headers']['X-DRC-API-Version'] = $this->config->getApiVersion();
        }

        try {
            $this->requestCount++;
            $response = $this->httpClient->request($method, $path, $options);
            return $this->parseResponse($response);
        } catch (RequestException $e) {
            return $this->handleException($e, $method, $path, $options, $retryCount);
        } catch (GuzzleException $e) {
            throw new ApiException(
                'HTTP request failed: ' . $e->getMessage(),
                0,
                [],
                'http_error',
                $e
            );
        }
    }

    private function handleException(
        RequestException $e,
        string $method,
        string $path,
        array $options,
        int $retryCount
    ): array {
        $response = $e->getResponse();
        $statusCode = $response ? $response->getStatusCode() : 0;
        $body = $response ? $this->parseResponse($response) : [];

        // Handle rate limiting with retry
        if ($statusCode === 429) {
            if ($retryCount < $this->config->getMaxRetries()) {
                $retryAfter = (int)($response->getHeader('Retry-After')[0] ?? 1);
                sleep($retryAfter);
                return $this->request($method, $path, $options, $retryCount + 1);
            }

            $exception = new RateLimitException(
                'Rate limit exceeded',
                $statusCode,
                $body,
                'rate_limit'
            );
            if ($response && $response->hasHeader('Retry-After')) {
                $exception->setRetryAfter((int)$response->getHeader('Retry-After')[0]);
            }
            throw $exception;
        }

        // Handle authentication errors
        if ($statusCode === 401) {
            throw new AuthenticationException(
                $body['error_description'] ?? $body['detail'] ?? 'Authentication failed',
                $statusCode,
                $body,
                'authentication_error'
            );
        }

        // Handle validation errors
        if ($statusCode === 400) {
            $exception = new ValidationException(
                $body['detail'] ?? 'Validation failed',
                $statusCode,
                $body,
                'validation_error'
            );
            if (isset($body['errors'])) {
                $exception->setValidationErrors($body['errors']);
            }
            throw $exception;
        }

        // Handle other client/server errors
        throw new ApiException(
            $body['detail'] ?? $body['error'] ?? 'API request failed',
            $statusCode,
            $body,
            $this->getErrorType($statusCode)
        );
    }

    private function parseResponse(ResponseInterface $response): array
    {
        $body = (string)$response->getBody();

        if (empty($body)) {
            return [];
        }

        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException(
                'Failed to parse JSON response: ' . json_last_error_msg(),
                $response->getStatusCode(),
                ['raw' => $body],
                'json_parse_error'
            );
        }

        return $decoded;
    }

    private function getAuthHeaders(): array
    {
        $headers = [];

        if ($this->config->hasAccessToken()) {
            $headers['Authorization'] = 'Bearer ' . $this->config->getAccessToken();
        }

        return $headers;
    }

    private function getErrorType(int $statusCode): string
    {
        return match (true) {
            $statusCode >= 500 => 'server_error',
            $statusCode === 404 => 'not_found',
            $statusCode === 403 => 'forbidden',
            $statusCode === 402 => 'payment_required',
            $statusCode >= 400 => 'client_error',
            default => 'unknown_error',
        };
    }

    public function getRequestCount(): int
    {
        return $this->requestCount;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }
}

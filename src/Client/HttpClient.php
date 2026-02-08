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
    private bool $useLaravelHttp = false;
    private mixed $laravelRequest = null;
    private mixed $tokenRefreshCallback = null;
    private ?int $rateLimitRemaining = null;
    private ?int $rateLimitLimit = null;
    private ?int $rateLimitResetAt = null;

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

        if ($config->getHttpClientType() === 'laravel') {
            $this->enableLaravelHttpIfAvailable();
        }
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

    public function pool(callable $callback): array
    {
        if (!$this->useLaravelHttp || !$this->laravelRequest) {
            throw new ApiException(
                'Laravel HTTP pool is only available when using the Laravel HTTP client.',
                0,
                [],
                'laravel_http_unavailable'
            );
        }

        if (!method_exists($this->laravelRequest, 'pool')) {
            throw new ApiException(
                'Laravel HTTP pool is not supported by the configured client.',
                0,
                [],
                'laravel_http_unavailable'
            );
        }

        return $this->laravelRequest->pool($callback);
    }

    private function request(string $method, string $path, array $options = [], int $retryCount = 0): array
    {
        $this->maybeRefreshToken();
        $this->throttleForRateLimit();

        $options['headers'] = array_merge(
            $options['headers'] ?? [],
            $this->getAuthHeaders()
        );

        if ($this->config->getApiVersion()) {
            $options['headers']['X-DRC-API-Version'] = $this->config->getApiVersion();
        }

        if ($this->useLaravelHttp && $this->laravelRequest) {
            return $this->requestWithLaravel($method, $path, $options, $retryCount);
        }

        return $this->requestWithGuzzle($method, $path, $options, $retryCount);
    }

    private function requestWithGuzzle(string $method, string $path, array $options, int $retryCount): array
    {
        try {
            $this->requestCount++;
            $response = $this->httpClient->request($method, $path, $options);
            $this->updateRateLimitFromHeaders($response->getHeaders());
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

    private function requestWithLaravel(string $method, string $path, array $options, int $retryCount): array
    {
        $request = $this->laravelRequest;

        if (method_exists($request, 'retry')) {
            $request = $request->retry($this->config->getMaxRetries(), $this->config->getRetryDelay());
        }

        foreach ($this->config->getLaravelMiddleware() as $middleware) {
            if (method_exists($request, 'withMiddleware')) {
                $request = $request->withMiddleware($middleware);
            }
        }

        $this->requestCount++;
        $response = $request->send($method, $path, $options);
        $headers = method_exists($response, 'headers') ? $response->headers() : [];
        $this->updateRateLimitFromHeaders($headers);

        if (method_exists($response, 'successful') && !$response->successful()) {
            return $this->handleErrorResponse(
                $response->status(),
                $this->parseResponseBody($response->body(), $response->status()),
                $headers,
                $method,
                $path,
                $options,
                $retryCount
            );
        }

        return $this->parseResponseBody($response->body(), $response->status());
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
        $headers = $response ? $response->getHeaders() : [];
        $this->updateRateLimitFromHeaders($headers);

        return $this->handleErrorResponse($statusCode, $body, $headers, $method, $path, $options, $retryCount);
    }

    private function parseResponse(ResponseInterface $response): array
    {
        return $this->parseResponseBody((string)$response->getBody(), $response->getStatusCode());
    }

    private function parseResponseBody(string $body, int $statusCode): array
    {
        if (empty($body)) {
            return [];
        }

        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException(
                'Failed to parse JSON response: ' . json_last_error_msg(),
                $statusCode,
                ['raw' => $body],
                'json_parse_error'
            );
        }

        return $decoded;
    }

    private function handleErrorResponse(
        int $statusCode,
        array $body,
        array $headers,
        string $method,
        string $path,
        array $options,
        int $retryCount
    ): array {
        if ($statusCode === 429) {
            if ($retryCount < $this->config->getMaxRetries()) {
                $retryAfter = $this->getRetryAfter($headers);
                sleep($retryAfter);
                return $this->request($method, $path, $options, $retryCount + 1);
            }

            $exception = new RateLimitException(
                'Rate limit exceeded',
                $statusCode,
                $body,
                'rate_limit'
            );
            $retryAfter = $this->getRetryAfter($headers);
            if ($retryAfter > 0) {
                $exception->setRetryAfter($retryAfter);
            }
            throw $exception;
        }

        if ($statusCode === 401) {
            throw new AuthenticationException(
                $body['error_description'] ?? $body['detail'] ?? 'Authentication failed',
                $statusCode,
                $body,
                'authentication_error'
            );
        }

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

        throw new ApiException(
            $body['detail'] ?? $body['error'] ?? 'API request failed',
            $statusCode,
            $body,
            $this->getErrorType($statusCode)
        );
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

    public function setTokenRefreshCallback(callable $callback): void
    {
        $this->tokenRefreshCallback = $callback;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getRateLimitState(): array
    {
        return [
            'limit' => $this->rateLimitLimit,
            'remaining' => $this->rateLimitRemaining,
            'reset_at' => $this->rateLimitResetAt,
        ];
    }

    public function usesLaravelHttp(): bool
    {
        return $this->useLaravelHttp;
    }

    private function maybeRefreshToken(): void
    {
        if (!$this->config->shouldAutoRefreshToken()) {
            return;
        }

        if ($this->tokenRefreshCallback && is_callable($this->tokenRefreshCallback)) {
            call_user_func($this->tokenRefreshCallback);
        }
    }

    private function throttleForRateLimit(): void
    {
        if (!$this->config->isRateLimitThrottleEnabled()) {
            return;
        }

        if ($this->rateLimitRemaining !== null && $this->rateLimitRemaining <= 0 && $this->rateLimitResetAt) {
            $sleepFor = $this->rateLimitResetAt - time();
            if ($sleepFor > 0) {
                sleep($sleepFor);
            }
        }
    }

    private function updateRateLimitFromHeaders(array $headers): void
    {
        $limit = $this->getHeaderValue($headers, 'X-RateLimit-Limit');
        $remaining = $this->getHeaderValue($headers, 'X-RateLimit-Remaining');
        $reset = $this->getHeaderValue($headers, 'X-RateLimit-Reset');

        if ($limit !== null) {
            $this->rateLimitLimit = (int)$limit;
        }

        if ($remaining !== null) {
            $this->rateLimitRemaining = (int)$remaining;
        }

        if ($reset !== null) {
            $resetInt = (int)$reset;
            if ($resetInt > 0) {
                // Some APIs return reset as a unix timestamp in seconds, others as seconds-until-reset,
                // and some return unix time in milliseconds. Normalize to unix timestamp in seconds.
                if ($resetInt > 1000000000000) {
                    $resetInt = (int)floor($resetInt / 1000);
                }

                $this->rateLimitResetAt = $resetInt < 1000000000 ? time() + $resetInt : $resetInt;
            }
        }
    }

    private function getHeaderValue(array $headers, string $key): ?string
    {
        $value = $headers[$key] ?? $headers[strtolower($key)] ?? null;

        if (is_array($value)) {
            return $value[0] ?? null;
        }

        if (is_string($value)) {
            return $value;
        }

        return null;
    }

    private function getRetryAfter(array $headers): int
    {
        $retryAfter = $this->getHeaderValue($headers, 'Retry-After');
        return $retryAfter ? (int)$retryAfter : 1;
    }

    private function enableLaravelHttpIfAvailable(): void
    {
        if (!class_exists(\Illuminate\Http\Client\PendingRequest::class)) {
            return;
        }

        $pendingRequest = $this->config->getLaravelPendingRequest();

        if (!$pendingRequest && class_exists(\Illuminate\Support\Facades\Http::class)) {
            $pendingRequest = \Illuminate\Support\Facades\Http::baseUrl($this->config->getBaseUri())
                ->timeout($this->config->getTimeout())
                ->connectTimeout($this->config->getConnectTimeout())
                ->acceptJson()
                ->withHeaders([
                    'User-Agent' => $this->config->getUserAgent(),
                ]);
        }

        if (!$pendingRequest) {
            return;
        }

        if (!($pendingRequest instanceof \Illuminate\Http\Client\PendingRequest)) {
            return;
        }

        $this->useLaravelHttp = true;
        $this->laravelRequest = $pendingRequest;
    }
}

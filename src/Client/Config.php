<?php

declare(strict_types=1);

namespace DrChrono\Client;

/**
 * Configuration class for DrChrono API client
 */
class Config
{
    private string $baseUri = 'https://app.drchrono.com';
    private ?string $clientId = null;
    private ?string $clientSecret = null;
    private ?string $redirectUri = null;
    private ?string $accessToken = null;
    private ?string $refreshToken = null;
    private ?int $tokenExpiresAt = null;
    private int $timeout = 30;
    private int $connectTimeout = 10;
    private string $userAgent = 'DrChrono-PHP-SDK/1.0';
    private bool $debug = false;
    private int $maxRetries = 3;
    private int $retryDelay = 1000; // milliseconds
    private ?string $apiVersion = null;

    public function __construct(array $config = [])
    {
        foreach ($config as $key => $value) {
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function getBaseUri(): string
    {
        return rtrim($this->baseUri, '/');
    }

    public function setBaseUri(string $baseUri): self
    {
        $this->baseUri = $baseUri;
        return $this;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }

    public function getClientSecret(): ?string
    {
        return $this->clientSecret;
    }

    public function setClientSecret(string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;
        return $this;
    }

    public function getRedirectUri(): ?string
    {
        return $this->redirectUri;
    }

    public function setRedirectUri(string $redirectUri): self
    {
        $this->redirectUri = $redirectUri;
        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function getTokenExpiresAt(): ?int
    {
        return $this->tokenExpiresAt;
    }

    public function setTokenExpiresAt(?int $tokenExpiresAt): self
    {
        $this->tokenExpiresAt = $tokenExpiresAt;
        return $this;
    }

    public function isTokenExpired(): bool
    {
        if ($this->tokenExpiresAt === null) {
            return false;
        }
        return time() >= $this->tokenExpiresAt - 300; // 5 minute buffer
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getConnectTimeout(): int
    {
        return $this->connectTimeout;
    }

    public function setConnectTimeout(int $connectTimeout): self
    {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    public function setUserAgent(string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }

    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    public function setMaxRetries(int $maxRetries): self
    {
        $this->maxRetries = $maxRetries;
        return $this;
    }

    public function getRetryDelay(): int
    {
        return $this->retryDelay;
    }

    public function setRetryDelay(int $retryDelay): self
    {
        $this->retryDelay = $retryDelay;
        return $this;
    }

    public function getApiVersion(): ?string
    {
        return $this->apiVersion;
    }

    public function setApiVersion(?string $apiVersion): self
    {
        $this->apiVersion = $apiVersion;
        return $this;
    }

    public function hasCredentials(): bool
    {
        return $this->clientId !== null && $this->clientSecret !== null;
    }

    public function hasAccessToken(): bool
    {
        return $this->accessToken !== null;
    }

    public function toArray(): array
    {
        return [
            'base_uri' => $this->baseUri,
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'token_expires_at' => $this->tokenExpiresAt,
            'timeout' => $this->timeout,
            'connect_timeout' => $this->connectTimeout,
            'user_agent' => $this->userAgent,
            'debug' => $this->debug,
            'max_retries' => $this->maxRetries,
            'retry_delay' => $this->retryDelay,
            'api_version' => $this->apiVersion,
        ];
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Auth;

use DrChrono\Client\Config;
use DrChrono\Client\HttpClient;
use DrChrono\Exception\AuthenticationException;

/**
 * Handles OAuth2 authentication flow for DrChrono API
 */
class OAuth2Handler
{
    private const AUTHORIZE_URI = '/o/authorize/';
    private const TOKEN_URI = '/o/token/';
    private const REVOKE_URI = '/o/revoke_token/';

    private Config $config;
    private HttpClient $httpClient;

    public function __construct(Config $config, HttpClient $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    /**
     * Build authorization URL for OAuth2 flow
     */
    public function getAuthorizationUrl(array $scopes = [], string $state = ''): string
    {
        if (!$this->config->hasCredentials()) {
            throw new AuthenticationException(
                'Client ID and redirect URI are required',
                0,
                [],
                'missing_credentials'
            );
        }

        $params = [
            'client_id' => $this->config->getClientId(),
            'redirect_uri' => $this->config->getRedirectUri(),
            'response_type' => 'code',
        ];

        if (!empty($scopes)) {
            $params['scope'] = implode(' ', $scopes);
        }

        if (!empty($state)) {
            $params['state'] = $state;
        }

        return $this->config->getBaseUri() . self::AUTHORIZE_URI . '?' . http_build_query($params);
    }

    /**
     * Exchange authorization code for access token
     */
    public function exchangeAuthorizationCode(string $code): array
    {
        if (!$this->config->hasCredentials()) {
            throw new AuthenticationException(
                'Client ID, client secret, and redirect URI are required',
                0,
                [],
                'missing_credentials'
            );
        }

        $data = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->config->getRedirectUri(),
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
        ];

        $response = $this->requestToken($data);
        $this->updateConfigFromTokenResponse($response);

        return $response;
    }

    /**
     * Refresh access token using refresh token
     */
    public function refreshAccessToken(?string $refreshToken = null): array
    {
        $refreshToken = $refreshToken ?? $this->config->getRefreshToken();

        if (!$refreshToken) {
            throw new AuthenticationException(
                'Refresh token is required',
                0,
                [],
                'missing_refresh_token'
            );
        }

        if (!$this->config->hasCredentials()) {
            throw new AuthenticationException(
                'Client ID and client secret are required',
                0,
                [],
                'missing_credentials'
            );
        }

        $data = [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
        ];

        $response = $this->requestToken($data);
        $this->updateConfigFromTokenResponse($response);

        return $response;
    }

    /**
     * Revoke access token
     */
    public function revokeToken(?string $token = null): void
    {
        $token = $token ?? $this->config->getAccessToken();

        if (!$token) {
            throw new AuthenticationException(
                'Token is required',
                0,
                [],
                'missing_token'
            );
        }

        if (!$this->config->hasCredentials()) {
            throw new AuthenticationException(
                'Client ID and client secret are required',
                0,
                [],
                'missing_credentials'
            );
        }

        $data = [
            'token' => $token,
            'client_id' => $this->config->getClientId(),
            'client_secret' => $this->config->getClientSecret(),
        ];

        $this->httpClient->post(self::REVOKE_URI, $data);

        // Clear tokens from config
        $this->config->setAccessToken(null);
        $this->config->setRefreshToken(null);
        $this->config->setTokenExpiresAt(null);
    }

    /**
     * Check if token needs refresh and refresh if necessary
     */
    public function ensureValidToken(): bool
    {
        if (!$this->config->hasAccessToken()) {
            return false;
        }

        if ($this->config->isTokenExpired() && $this->config->getRefreshToken()) {
            try {
                $this->refreshAccessToken();
                return true;
            } catch (AuthenticationException $e) {
                return false;
            }
        }

        return true;
    }

    private function requestToken(array $data): array
    {
        try {
            // Create a temporary HTTP client without auth headers for token requests
            $tempConfig = clone $this->config;
            $tempConfig->setAccessToken(null);
            $tempClient = new HttpClient($tempConfig);

            return $tempClient->post(self::TOKEN_URI, $data);
        } catch (AuthenticationException $e) {
            throw new AuthenticationException(
                $e->getMessage(),
                $e->getStatusCode(),
                $e->getResponseBody(),
                'token_request_failed',
                $e
            );
        }
    }

    private function updateConfigFromTokenResponse(array $response): void
    {
        if (isset($response['access_token'])) {
            $this->config->setAccessToken($response['access_token']);
        }

        if (isset($response['refresh_token'])) {
            $this->config->setRefreshToken($response['refresh_token']);
        }

        if (isset($response['expires_in'])) {
            $expiresAt = time() + (int)$response['expires_in'];
            $this->config->setTokenExpiresAt($expiresAt);
        }
    }
}

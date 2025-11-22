<?php

declare(strict_types=1);

namespace DrChrono\Webhook;

use DrChrono\Exception\WebhookVerificationException;

/**
 * Webhook signature verification for DrChrono webhooks
 *
 * DrChrono uses HMAC SHA-256 signatures for iframe integration tokens.
 * This class provides verification helpers for webhook security.
 */
class WebhookVerifier
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * Verify HMAC signature
     */
    public function verify(string $payload, string $signature, string $algorithm = 'sha256'): bool
    {
        $expectedSignature = hash_hmac($algorithm, $payload, $this->secret);
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify and parse webhook payload
     *
     * @throws WebhookVerificationException
     */
    public function verifyAndParse(
        string $payload,
        ?string $signature = null,
        ?array $headers = null
    ): WebhookEvent {
        // If signature provided, verify it
        if ($signature !== null) {
            if (!$this->verify($payload, $signature)) {
                throw new WebhookVerificationException('Invalid webhook signature');
            }
        }

        // Parse JSON payload
        $data = json_decode($payload, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new WebhookVerificationException(
                'Invalid JSON payload: ' . json_last_error_msg()
            );
        }

        return WebhookEvent::fromArray($data);
    }

    /**
     * Verify webhook from request (auto-detect signature from headers)
     *
     * Common signature header patterns:
     * - X-DrChrono-Signature
     * - X-Signature
     * - HTTP_X_DRCHRONO_SIGNATURE (from $_SERVER)
     */
    public function verifyFromRequest(
        string $payload,
        array $headers = [],
        bool $requireSignature = false
    ): WebhookEvent {
        $signature = $this->extractSignature($headers);

        if ($requireSignature && $signature === null) {
            throw new WebhookVerificationException('Missing webhook signature');
        }

        return $this->verifyAndParse($payload, $signature, $headers);
    }

    /**
     * Extract signature from headers
     */
    private function extractSignature(array $headers): ?string
    {
        // Normalize header keys to lowercase
        $headers = array_change_key_case($headers, CASE_LOWER);

        // Try common signature header names
        $signatureHeaders = [
            'x-drchrono-signature',
            'x-signature',
            'signature',
            'http_x_drchrono_signature',
        ];

        foreach ($signatureHeaders as $header) {
            if (isset($headers[$header])) {
                return $headers[$header];
            }
        }

        return null;
    }

    /**
     * Generate signature for testing
     */
    public function generateSignature(string $payload, string $algorithm = 'sha256'): string
    {
        return hash_hmac($algorithm, $payload, $this->secret);
    }

    /**
     * Verify JWT token (for iframe integration)
     */
    public function verifyJWT(string $token): array
    {
        // Split JWT token
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new WebhookVerificationException('Invalid JWT format');
        }

        [$header, $payload, $signature] = $parts;

        // Verify signature
        $dataToSign = "{$header}.{$payload}";
        $expectedSignature = $this->base64UrlEncode(
            hash_hmac('sha256', $dataToSign, $this->secret, true)
        );

        if (!hash_equals($expectedSignature, $signature)) {
            throw new WebhookVerificationException('Invalid JWT signature');
        }

        // Decode payload
        $decodedPayload = json_decode($this->base64UrlDecode($payload), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new WebhookVerificationException('Invalid JWT payload');
        }

        return $decodedPayload;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Exception;

/**
 * Exception thrown when API rate limit is exceeded
 */
class RateLimitException extends ApiException
{
    protected ?int $retryAfter = null;

    public function setRetryAfter(int $seconds): void
    {
        $this->retryAfter = $seconds;
    }

    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}

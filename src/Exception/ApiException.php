<?php

declare(strict_types=1);

namespace DrChrono\Exception;

use Throwable;

/**
 * Exception thrown when API returns an error response
 */
class ApiException extends DrChronoException
{
    protected int $statusCode;
    protected array $responseBody;
    protected string $errorType;

    public function __construct(
        string $message,
        int $statusCode,
        array $responseBody = [],
        string $errorType = '',
        ?Throwable $previous = null
    ) {
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
        $this->errorType = $errorType;

        parent::__construct($message, $statusCode, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponseBody(): array
    {
        return $this->responseBody;
    }

    public function getErrorType(): string
    {
        return $this->errorType;
    }

    public function getErrorDetails(): array
    {
        return [
            'status_code' => $this->statusCode,
            'error_type' => $this->errorType,
            'message' => $this->message,
            'response_body' => $this->responseBody,
        ];
    }
}

<?php

declare(strict_types=1);

namespace DrChrono\Exception;

/**
 * Exception thrown when API returns validation errors
 */
class ValidationException extends ApiException
{
    protected array $validationErrors = [];

    public function setValidationErrors(array $errors): void
    {
        $this->validationErrors = $errors;
    }

    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    public function hasValidationErrors(): bool
    {
        return !empty($this->validationErrors);
    }
}

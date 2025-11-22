<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Lab Tests resource - manage laboratory tests
 */
class LabTestsResource extends AbstractResource
{
    protected string $resourcePath = '/api/lab_tests';

    /**
     * List lab tests
     */
    public function listAll(array $filters = []): PagedCollection
    {
        return $this->list($filters);
    }

    /**
     * Get lab test details
     */
    public function getTest(int $testId): array
    {
        return $this->get($testId);
    }

    /**
     * Create lab test
     */
    public function createTest(array $testData): array
    {
        return $this->create($testData);
    }

    /**
     * Update lab test
     */
    public function updateTest(int $testId, array $testData): array
    {
        return $this->update($testId, $testData);
    }
}

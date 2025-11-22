<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Billing resource - manage billing and transactions
 */
class BillingResource extends AbstractResource
{
    protected string $resourcePath = '/api/line_items';

    /**
     * List line items (billing codes)
     */
    public function listLineItems(array $filters = []): PagedCollection
    {
        return $this->list($filters);
    }

    /**
     * List line items by appointment
     */
    public function listByAppointment(int $appointmentId): PagedCollection
    {
        return $this->list(['appointment' => $appointmentId]);
    }

    /**
     * List line items by doctor
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * Create line item
     */
    public function createLineItem(array $lineItemData): array
    {
        return $this->create($lineItemData);
    }

    /**
     * Update line item
     */
    public function updateLineItem(int $lineItemId, array $lineItemData): array
    {
        return $this->update($lineItemId, $lineItemData);
    }

    /**
     * Delete line item
     */
    public function deleteLineItem(int $lineItemId): void
    {
        $this->delete($lineItemId);
    }

    /**
     * Get transactions
     */
    public function getTransactions(array $filters = []): array
    {
        return $this->httpClient->get('/api/transactions', $filters);
    }
}

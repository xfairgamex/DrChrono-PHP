<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Lab Orders resource - manage laboratory orders
 */
class LabOrdersResource extends AbstractResource
{
    protected string $resourcePath = '/api/lab_orders';

    /**
     * List lab orders for patient
     */
    public function listByPatient(int $patientId, array $filters = []): PagedCollection
    {
        $filters['patient'] = $patientId;
        return $this->list($filters);
    }

    /**
     * List lab orders by doctor
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * Create lab order
     */
    public function createOrder(array $orderData): array
    {
        return $this->create($orderData);
    }

    /**
     * Update lab order
     */
    public function updateOrder(int $orderId, array $orderData): array
    {
        return $this->update($orderId, $orderData);
    }

    /**
     * Get lab order details
     */
    public function getOrder(int $orderId): array
    {
        return $this->get($orderId);
    }

    /**
     * Get lab order document (requisition form)
     */
    public function getOrderDocument(int $orderId): array
    {
        return $this->httpClient->get("/api/lab_orders/{$orderId}/document");
    }

    /**
     * Get lab orders summary
     *
     * Returns a summary view of lab orders, typically with aggregated
     * information and status counts. This endpoint provides a high-level
     * overview of lab orders.
     *
     * @param array $filters Optional filters (patient, doctor, date_range, etc.)
     * @return array Lab orders summary data
     */
    public function getSummary(array $filters = []): array
    {
        return $this->httpClient->get('/api/lab_orders_summary', $filters);
    }
}

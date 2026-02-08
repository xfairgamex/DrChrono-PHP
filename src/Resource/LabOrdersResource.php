<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Lab Orders resource - manage laboratory orders
 *
 * This resource handles creating lab orders, tracking results, and managing
 * laboratory requisition forms for diagnostic testing.
 *
 * @see https://app.drchrono.com/api-docs/#tag/Lab-Orders Official API documentation
 */
class LabOrdersResource extends AbstractResource
{
    protected string $resourcePath = '/api/lab_orders';

    /**
     * List lab orders for a specific patient
     *
     * @param int $patientId Patient ID
     * @param array $filters Optional additional filters (doctor, since, status)
     * @return PagedCollection Paginated lab order results
     *
     * @example
     * // Get all lab orders for a patient
     * $orders = $client->labOrders->listByPatient(12345);
     *
     * // Get pending lab orders
     * $orders = $client->labOrders->listByPatient(12345, [
     *     'status' => 'pending',
     * ]);
     *
     * foreach ($orders as $order) {
     *     echo "Order {$order['id']}: {$order['lab_test']}\n";
     *     echo "Status: {$order['status']}\n";
     * }
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
     * Create a new lab order
     *
     * @param array $orderData Lab order data
     * @return array Created lab order data
     *
     * Required fields:
     * - doctor (int): Ordering doctor ID
     * - patient (int): Patient ID
     * - lab_test (string): Lab test name or code
     *
     * @example
     * // Create a basic lab order
     * $order = $client->labOrders->createOrder([
     *     'doctor' => 123,
     *     'patient' => 456,
     *     'lab_test' => 'CBC with Differential',
     *     'priority' => 'routine',
     *     'clinical_info' => 'Annual physical',
     * ]);
     *
     * echo "Created lab order ID: {$order['id']}\n";
     *
     * // Create urgent lab order
     * $order = $client->labOrders->createOrder([
     *     'doctor' => 123,
     *     'patient' => 456,
     *     'lab_test' => 'Troponin I',
     *     'priority' => 'stat',
     *     'clinical_info' => 'Chest pain, rule out MI',
     * ]);
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
     *
     * Retrieves the printable lab requisition form for the order.
     *
     * @param int $orderId Lab order ID
     * @return array Document data including URL
     *
     * @example
     * $document = $client->labOrders->getOrderDocument(12345);
     * echo "Requisition form URL: {$document['document_url']}\n";
     *
     * // Download and print requisition
     * $pdfContent = file_get_contents($document['document_url']);
     * file_put_contents('/path/to/save/requisition.pdf', $pdfContent);
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

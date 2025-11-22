<?php

/**
 * Example 5: Webhook Handler
 *
 * Demonstrates handling and verifying DrChrono webhooks.
 * This script should be deployed as a web endpoint.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\Webhook\WebhookVerifier;
use DrChrono\Webhook\WebhookEvent;
use DrChrono\Exception\WebhookVerificationException;

// Your webhook secret (client secret for iframe integration)
$webhookSecret = getenv('DRCHRONO_CLIENT_SECRET') ?: 'your_client_secret';

// Create webhook verifier
$verifier = new WebhookVerifier($webhookSecret);

// Get raw POST body
$payload = file_get_contents('php://input');

// Get headers (handle both Apache and nginx)
$headers = getallheaders() ?: [];
if (empty($headers) && function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
}

// Log incoming webhook
error_log("Webhook received: " . substr($payload, 0, 200));

try {
    // Verify and parse webhook
    // Set requireSignature to true if you're enforcing signature verification
    $event = $verifier->verifyFromRequest($payload, $headers, requireSignature: false);

    echo "Webhook verified successfully\n";

    // Handle different event types
    switch ($event->getEvent()) {
        case 'appointment.created':
            handleAppointmentCreated($event);
            break;

        case 'appointment.updated':
            handleAppointmentUpdated($event);
            break;

        case 'appointment.deleted':
            handleAppointmentDeleted($event);
            break;

        case 'patient.created':
            handlePatientCreated($event);
            break;

        case 'patient.updated':
            handlePatientUpdated($event);
            break;

        default:
            handleUnknownEvent($event);
            break;
    }

    // Return 200 OK to acknowledge receipt
    http_response_code(200);
    echo json_encode(['status' => 'success', 'event' => $event->getEvent()]);

} catch (WebhookVerificationException $e) {
    error_log("Webhook verification failed: " . $e->getMessage());
    http_response_code(401);
    echo json_encode(['error' => 'Invalid webhook signature']);

} catch (\Exception $e) {
    error_log("Webhook processing error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}

// ============================================================================
// Event Handlers
// ============================================================================

function handleAppointmentCreated(WebhookEvent $event): void
{
    $appointmentId = $event->getAppointmentId();
    $data = $event->getData();

    error_log("New appointment created: {$appointmentId}");

    // Your business logic here
    // - Send confirmation email
    // - Update external calendar
    // - Trigger notifications
}

function handleAppointmentUpdated(WebhookEvent $event): void
{
    $appointmentId = $event->getAppointmentId();
    $data = $event->getData();

    error_log("Appointment updated: {$appointmentId}");

    // Your business logic here
    // - Notify patient of changes
    // - Update integrations
}

function handleAppointmentDeleted(WebhookEvent $event): void
{
    $appointmentId = $event->getAppointmentId();

    error_log("Appointment deleted: {$appointmentId}");

    // Your business logic here
    // - Send cancellation notification
    // - Update records
}

function handlePatientCreated(WebhookEvent $event): void
{
    $patientId = $event->getPatientId();
    $data = $event->getData();

    error_log("New patient created: {$patientId}");

    // Your business logic here
    // - Send welcome email
    // - Create patient portal account
    // - Update CRM
}

function handlePatientUpdated(WebhookEvent $event): void
{
    $patientId = $event->getPatientId();

    error_log("Patient updated: {$patientId}");

    // Your business logic here
    // - Sync updated information
}

function handleUnknownEvent(WebhookEvent $event): void
{
    error_log("Unknown webhook event: " . $event->getEvent());
    // Log for monitoring
}

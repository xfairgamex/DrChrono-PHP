# DrChrono PHP SDK

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

PHP SDK for the DrChrono API - EHR/Practice Management integration for PHP applications.

## Features

✨ **Complete API Coverage** - All DrChrono API endpoints supported
🔐 **OAuth2 Authentication** - Full OAuth2 flow with automatic token refresh
📄 **Pagination Support** - Automatic pagination with generators for memory efficiency
🎯 **Type-Safe Models** - Optional DTOs for common entities (Patient, Appointment, etc.)
🔔 **Webhook Handling** - Built-in webhook verification and event parsing
⚡ **Smart Retry Logic** - Automatic retry with exponential backoff for rate limits
🛡️ **Error Handling** - Granular exceptions for different error types
📦 **Framework Agnostic** - Works with Laravel, Symfony, or plain PHP

## Requirements

- PHP 8.1 or higher
- Composer
- ext-json

## Installation

Install via Composer:

```bash
composer require drchrono/php-sdk
```

## Quick Start

### Basic Authentication

```php
use DrChrono\DrChronoClient;

// Create client with access token
$client = DrChronoClient::withAccessToken('your_access_token');

// Get current user
$user = $client->getCurrentUser();
echo "Authenticated as: {$user['first_name']} {$user['last_name']}";

// List patients
$patients = $client->patients->list();
foreach ($patients as $patient) {
    echo "{$patient['first_name']} {$patient['last_name']}\n";
}
```

### OAuth2 Flow

```php
use DrChrono\DrChronoClient;

// Initialize client with OAuth credentials
$client = DrChronoClient::withOAuth(
    clientId: 'your_client_id',
    clientSecret: 'your_client_secret',
    redirectUri: 'https://yourapp.com/callback'
);

// Step 1: Redirect user to authorization URL
$authUrl = $client->auth()->getAuthorizationUrl(
    scopes: ['patients:read', 'appointments:read', 'appointments:write']
);
header("Location: {$authUrl}");

// Step 2: Exchange code for tokens (in your callback handler)
$tokens = $client->auth()->exchangeAuthorizationCode($_GET['code']);

// Step 3: Use access token
$client->getConfig()->setAccessToken($tokens['access_token']);
$patients = $client->patients->list();
```

## Core Concepts

### Resources

The SDK organizes API endpoints into resource classes:

```php
$client->patients        // Patient management
$client->appointments    // Appointment scheduling
$client->clinicalNotes   // Clinical documentation
$client->documents       // Document management
$client->offices         // Office locations
$client->users           // Doctors and staff
$client->tasks           // Task management
$client->prescriptions   // Medication prescriptions
$client->labOrders       // Laboratory orders
$client->labResults      // Lab results
$client->insurances      // Insurance information
$client->allergies       // Patient allergies
$client->medications     // Patient medications
$client->problems        // Problem list
$client->vitals          // Vital signs
$client->immunizations   // Vaccination records
$client->billing         // Billing and transactions
$client->appointmentProfiles    // Appointment types
$client->appointmentTemplates   // Recurring blocks
$client->patientPayments        // Payment records
$client->patientMessages        // Patient communications
$client->inventoryCategories    // Inventory organization
$client->patientVaccineRecords  // Immunization tracking
$client->taskTemplates          // Reusable task templates
$client->taskCategories         // Task organization
$client->taskStatuses           // Custom task statuses
$client->taskNotes              // Task documentation
```

### Verbose Mode

Some endpoints support verbose mode to include additional related data that requires extra database queries:

```php
// Get patient with full insurance details
$patient = $client->patients->getWithInsurance($patientId);
echo "Insurance: {$patient['primary_insurance']['insurance_company']}\n";

// Get appointment with clinical data (vitals, notes, etc.)
$appointment = $client->appointments->getWithClinicalData($appointmentId);

// List patients with insurance details
// Note: Page size limited to 50 (vs 250 default)
$patients = $client->patients->listWithInsurance(['doctor' => 123456]);

// Manual verbose mode
$appointments = $client->appointments->list(['verbose' => 'true']);
```

**Verbose mode includes:**
- **Patients**: Full insurance objects, custom demographics, patient flags
- **Appointments**: Clinical notes, vitals, status history, reminders
- **Clinical Notes**: Detailed section content

**Performance considerations:**
- Page size reduced from 250 to 50 records
- Response time 2-5x slower per request
- Use only when you need the extra fields

See `examples/07_verbose_mode.php` for detailed examples.

### Pagination

All list endpoints return paginated results:

```php
// Get first page
$patients = $client->patients->list(['page_size' => 50]);

echo "Page count: {$patients->count()}\n";
echo "Has more: " . ($patients->hasNext() ? 'Yes' : 'No') . "\n";

// Iterate through items
foreach ($patients as $patient) {
    // Process patient
}

// Auto-iterate all pages (memory efficient)
foreach ($client->patients->iterateAll() as $patient) {
    // Processes all patients across all pages
}

// Get all at once (caution: may be memory intensive)
$allPatients = $client->patients->all();
```

### Models

Use type-safe models for better IDE support:

```php
use DrChrono\Model\Patient;
use DrChrono\Model\Appointment;

// Create from array
$patient = Patient::fromArray($patientData);

echo $patient->getFullName();
echo $patient->getEmail();
echo $patient->getDateOfBirth();

// Convert to array
$data = $patient->toArray();

// Use models with resources
$appointmentData = (new Appointment())
    ->setDoctor(123)
    ->setPatient(456)
    ->setOffice(1)
    ->setDuration(30)
    ->setScheduledTime('2025-01-15T10:00:00')
    ->toArray();

$created = $client->appointments->create($appointmentData);
```

### Error Handling

The SDK provides granular exception classes:

```php
use DrChrono\Exception\AuthenticationException;
use DrChrono\Exception\ValidationException;
use DrChrono\Exception\RateLimitException;
use DrChrono\Exception\ApiException;

try {
    $patient = $client->patients->create($data);
} catch (ValidationException $e) {
    // Handle validation errors
    echo "Validation failed: {$e->getMessage()}\n";
    print_r($e->getValidationErrors());
} catch (RateLimitException $e) {
    // Handle rate limiting
    echo "Rate limited. Retry after: {$e->getRetryAfter()} seconds\n";
} catch (AuthenticationException $e) {
    // Handle authentication errors
    echo "Auth failed: {$e->getMessage()}\n";
} catch (ApiException $e) {
    // Handle other API errors
    echo "API error: {$e->getMessage()}\n";
    echo "Status: {$e->getStatusCode()}\n";
    print_r($e->getErrorDetails());
}
```

## Usage Examples

### Patient Management

```php
// Search patients
$results = $client->patients->search([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'date_of_birth' => '1980-01-01'
]);

// Create patient
$patient = $client->patients->createPatient([
    'first_name' => 'Jane',
    'last_name' => 'Smith',
    'gender' => 'Female',
    'date_of_birth' => '1985-03-15',
    'email' => 'jane@example.com',
    'doctor' => 123456,
]);

// Update patient
$client->patients->updateDemographics($patient['id'], [
    'cell_phone' => '555-1234'
]);

// Get patient summary
$summary = $client->patients->getSummary($patient['id']);

// Get CCDA
$ccda = $client->patients->getCCDA($patient['id']);
```

### Appointment Management

```php
// List appointments by date range
$appointments = $client->appointments->listByDateRange(
    startDate: '2025-01-01',
    endDate: '2025-01-31'
);

// List by patient
$patientAppts = $client->appointments->listByPatient($patientId);

// Create appointment
$appointment = $client->appointments->createAppointment([
    'doctor' => 123456,
    'patient' => 789012,
    'office' => 1,
    'duration' => 30,
    'scheduled_time' => '2025-01-15T10:00:00',
    'status' => 'Scheduled',
    'reason' => 'Annual checkup',
]);

// Update status
$client->appointments->setStatus($appointment['id'], 'Confirmed');

// Mark as arrived
$client->appointments->markArrived($appointment['id']);

// Mark as complete
$client->appointments->markComplete($appointment['id']);

// Cancel appointment
$client->appointments->cancel($appointment['id'], 'Patient requested cancellation');
```

### Clinical Documentation

```php
// Create clinical note
$note = $client->clinicalNotes->createNote([
    'patient' => $patientId,
    'appointment' => $appointmentId,
    'doctor' => $doctorId,
    'chief_complaint' => 'Follow-up visit',
]);

// Update note
$client->clinicalNotes->updateNote($note['id'], [
    'assessment' => 'Patient improving',
    'plan' => 'Continue current treatment',
]);

// Lock note (finalize)
$client->clinicalNotes->lock($note['id']);

// Get note templates
$templates = $client->clinicalNotes->getTemplates();
```

### Document Upload

```php
// Upload document to patient chart
$document = $client->documents->upload(
    doctorId: 123456,
    patientId: 789012,
    filePath: '/path/to/document.pdf',
    description: 'Lab results',
    date: '2025-01-15',
    metatags: ['Lab Results', 'Bloodwork']
);

// List patient documents
$documents = $client->documents->listByPatient($patientId);

// Update metadata
$client->documents->updateMetadata($document['id'], [
    'description' => 'Updated description'
]);
```

### Laboratory Orders

```php
// Create lab order
$order = $client->labOrders->createOrder([
    'patient' => $patientId,
    'doctor' => $doctorId,
    'order_type' => 'Lab',
]);

// List lab orders
$orders = $client->labOrders->listByPatient($patientId);

// Get order document (requisition)
$requisition = $client->labOrders->getOrderDocument($order['id']);
```

### Tasks

```php
// Create task
$task = $client->tasks->createTask([
    'title' => 'Follow up with patient',
    'patient' => $patientId,
    'assignee' => $userId,
    'due_date' => '2025-01-20',
    'status' => 'Open',
]);

// List tasks by patient
$tasks = $client->tasks->listByPatient($patientId);

// Mark as complete
$client->tasks->markComplete($task['id']);

// Add note to task
$client->tasks->addNote($task['id'], 'Patient contacted successfully');
```

## Webhooks

### Webhook Verification

```php
use DrChrono\Webhook\WebhookVerifier;

$verifier = new WebhookVerifier('your_client_secret');

// Get raw payload
$payload = file_get_contents('php://input');
$headers = getallheaders();

try {
    // Verify and parse webhook
    $event = $verifier->verifyFromRequest($payload, $headers);

    // Handle event
    if ($event->is('appointment.created')) {
        $appointmentId = $event->getAppointmentId();
        // Handle new appointment
    }

    if ($event->isPatientEvent()) {
        $patientId = $event->getPatientId();
        // Handle patient event
    }

    // Return success
    http_response_code(200);

} catch (\DrChrono\Exception\WebhookVerificationException $e) {
    http_response_code(401);
    echo "Invalid signature";
}
```

See [examples/05_webhook_handler.php](examples/05_webhook_handler.php) for a complete webhook handler.

## Configuration

### Advanced Configuration

```php
use DrChrono\Client\Config;
use DrChrono\DrChronoClient;

$config = new Config([
    'access_token' => 'your_token',
    'client_id' => 'your_client_id',
    'client_secret' => 'your_client_secret',
    'timeout' => 60,              // Request timeout (seconds)
    'connect_timeout' => 10,      // Connection timeout (seconds)
    'max_retries' => 3,           // Max retry attempts for rate limits
    'retry_delay' => 1000,        // Initial retry delay (milliseconds)
    'debug' => true,              // Enable debug mode
    'api_version' => 'v4',        // Specific API version
]);

$client = new DrChronoClient($config);
```

### Token Management

```php
// Check if token is expired
if ($client->getConfig()->isTokenExpired()) {
    // Refresh token
    $tokens = $client->auth()->refreshAccessToken();

    // Update config with new token
    $client->getConfig()->setAccessToken($tokens['access_token']);
}

// Auto-refresh (recommended)
$client->auth()->ensureValidToken();
```

## Testing

Run the test suite:

```bash
# Run all tests
composer test

# Run with coverage
composer test:coverage

# Run PHPStan static analysis
composer phpstan

# Check code style
composer cs:check

# Fix code style
composer cs:fix
```

## Examples

Runnable examples are provided in the `examples/` directory:

- `01_auth_basic.php` - Basic authentication with access token
- `02_auth_oauth_flow.php` - Complete OAuth2 flow
- `03_patients_crud.php` - Patient management (CRUD operations)
- `04_appointments_crud.php` - Appointment scheduling
- `05_webhook_handler.php` - Webhook verification and handling
- `06_clinical_workflow.php` - Complete clinical workflow example
- `07_verbose_mode.php` - Using verbose mode for additional data

## API Reference

### Available Resources

| Resource | Description | Example |
|----------|-------------|---------|
| `patients` | Patient demographics and records | `$client->patients->list()` |
| `appointments` | Appointment scheduling | `$client->appointments->listByDateRange()` |
| `appointmentProfiles` | Appointment types & durations | `$client->appointmentProfiles->listByDoctor()` |
| `appointmentTemplates` | Recurring appointment blocks | `$client->appointmentTemplates->createTemplate()` |
| `customAppointmentFields` | Custom appointment metadata | `$client->customAppointmentFields->listByDoctor()` |
| `clinicalNotes` | Clinical documentation | `$client->clinicalNotes->createNote()` |
| `documents` | Document management | `$client->documents->upload()` |
| `offices` | Office locations | `$client->offices->listAll()` |
| `users` | Doctors and staff | `$client->users->getCurrent()` |
| `tasks` | Task management | `$client->tasks->createTask()` |
| `prescriptions` | Medications | `$client->prescriptions->listByPatient()` |
| `labOrders` | Lab orders | `$client->labOrders->createOrder()` |
| `labResults` | Lab results | `$client->labResults->listByPatient()` |
| `insurances` | Insurance info | `$client->insurances->listByPatient()` |
| `allergies` | Patient allergies | `$client->allergies->createAllergy()` |
| `medications` | Patient medications | `$client->medications->listByPatient()` |
| `problems` | Problem list | `$client->problems->createProblem()` |
| `vitals` | Vital signs | `$client->vitals->createVitals()` |
| `immunizations` | Vaccination records | `$client->immunizations->listByPatient()` |
| `patientPayments` | Patient payment records | `$client->patientPayments->listByPatient()` |
| `patientMessages` | Patient communications | `$client->patientMessages->sendMessage()` |
| `patientsSummary` | Bulk patient summaries | `$client->patientsSummary->listByDoctor()` |
| `customDemographics` | Custom patient fields | `$client->customDemographics->createField()` |
| `patientFlagTypes` | Custom patient flags | `$client->patientFlagTypes->createFlagType()` |
| `billing` | Billing/transactions | `$client->billing->listLineItems()` |
| `billingProfiles` | Billing configurations | `$client->billingProfiles->getByDoctor()` |
| `eligibilityChecks` | Insurance verification | `$client->eligibilityChecks->verifyPrimaryInsurance()` |
| `feeSchedules` | Pricing and fee schedules | `$client->feeSchedules->getByCode()` |
| `transactions` | Payment transactions | `$client->transactions->recordPayment()` |
| `lineItems` | Invoice line items | `$client->lineItems->addProcedure()` |
| `patientPaymentLog` | Payment history/audit | `$client->patientPaymentLog->getPaymentHistory()` |
| `consentForms` | Patient consent forms | `$client->consentForms->markAsSigned()` |
| `customInsurancePlanNames` | Custom insurance naming | `$client->customInsurancePlanNames->setCustomName()` |
| `clinicalNoteTemplates` | Note templates | `$client->clinicalNoteTemplates->createTemplate()` |
| `clinicalNoteFieldTypes` | Custom note fields | `$client->clinicalNoteFieldTypes->createFieldType()` |
| `clinicalNoteFieldValues` | Note field values | `$client->clinicalNoteFieldValues->upsertValue()` |
| `procedures` | Medical procedures | `$client->procedures->createProcedure()` |
| `amendments` | Record amendments | `$client->amendments->approve()` |
| `carePlans` | Patient care plans | `$client->carePlans->createCarePlan()` |
| `patientRiskAssessments` | Risk evaluations | `$client->patientRiskAssessments->createAssessment()` |
| `patientPhysicalExams` | Physical exam records | `$client->patientPhysicalExams->createExam()` |
| `patientInterventions` | Treatment interventions | `$client->patientInterventions->createIntervention()` |
| `patientCommunications` | Patient communications | `$client->patientCommunications->createCommunication()` |
| `implantableDevices` | Implanted devices | `$client->implantableDevices->createDevice()` |

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This SDK is open-sourced software licensed under the [MIT license](LICENSE).

## Support

- **Documentation**: [DrChrono API Docs](https://app.drchrono.com/api-docs/)
- **Issues**: [GitHub Issues](https://github.com/drchrono/DrChrono-PHP/issues)
- **Email**: api@drchrono.com

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and updates.

---

Made with ❤️ for the healthcare community

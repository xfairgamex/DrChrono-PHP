# Verbose Mode Guide

## Overview

The DrChrono API supports a `verbose=true` query parameter for certain endpoints that includes additional fields requiring extra database queries. This guide explains how to use verbose mode effectively with the PHP SDK.

## What is Verbose Mode?

Verbose mode returns **expanded data** that includes related records and additional fields not returned in the default response. This is useful when you need complete information about a resource but comes with performance trade-offs.

### Performance Considerations

⚠️ **Important Trade-offs:**

- **Page Size Limit**: Verbose mode reduces page size from **250 to 50 records**
- **Response Time**: Each record requires additional database queries (slower responses)
- **API Rate Limits**: More requests needed for large datasets
- **Memory Usage**: Larger response payloads

**Best Practice**: Only use verbose mode when you need the additional fields. For bulk operations or simple listings, use standard mode.

---

## Supported Resources

The SDK provides verbose mode support for three primary resources:

### 1. Appointments (`AppointmentsResource`)
### 2. Patients (`PatientsResource`)
### 3. Clinical Notes (`ClinicalNotesResource`)

Each resource provides both **convenience methods** and **low-level access** to verbose mode.

---

## Appointments - Verbose Mode

### Additional Fields Included

When you use verbose mode with appointments, the following fields are included:

| Field | Description |
|-------|-------------|
| `clinical_note` | Full clinical note associated with the appointment |
| `vitals` | Standard vital signs (BP, temp, pulse, etc.) |
| `custom_vitals` | Custom vital sign measurements |
| `status_transitions` | Complete history of appointment status changes |
| `reminders` | Configured reminder settings for the appointment |
| `extended_updated_at` | Extended timestamp information |

### Usage Examples

#### Get Single Appointment with Clinical Data

```php
use DrChrono\DrChronoClient;

$client = DrChronoClient::withAccessToken('your_access_token');

// Option 1: Using convenience method (recommended)
$appointment = $client->appointments->getWithClinicalData(12345);

// Access verbose fields
echo "Clinical Note ID: {$appointment['clinical_note']['id']}\n";
echo "Blood Pressure: {$appointment['vitals']['blood_pressure_1']}/{$appointment['vitals']['blood_pressure_2']}\n";
echo "Status History: " . count($appointment['status_transitions']) . " changes\n";

// Option 2: Using base verbose method
$appointment = $client->appointments->getVerbose(12345);
```

#### List Appointments with Clinical Data

```php
// Get appointments for a date range with clinical data
$appointments = $client->appointments->listWithClinicalData([
    'date_range' => '2024-01-01/2024-01-31',
    'doctor' => 123,
]);

foreach ($appointments as $appt) {
    if (isset($appt['clinical_note'])) {
        echo "Appointment {$appt['id']} has clinical note: {$appt['clinical_note']['id']}\n";
    }

    if (isset($appt['vitals']) && $appt['vitals']) {
        echo "Temperature: {$appt['vitals']['temperature']}\n";
    }
}

// Total results (note: max 50 per page in verbose mode)
echo "Total: {$appointments->count()}\n";
```

#### Filter by Patient with Verbose Data

```php
// Get all appointments for a patient with full clinical details
$appointments = $client->appointments->listByPatient(98765, [
    'verbose' => 'true',  // Manual verbose mode
    'status' => 'Complete',
]);

foreach ($appointments as $appt) {
    // Process with clinical data
    if (!empty($appt['status_transitions'])) {
        $lastTransition = end($appt['status_transitions']);
        echo "Last status change: {$lastTransition['status']} at {$lastTransition['created_at']}\n";
    }
}
```

---

## Patients - Verbose Mode

### Additional Fields Included

When you use verbose mode with patients, the following fields are included:

| Field | Description |
|-------|-------------|
| `primary_insurance` | Complete primary insurance details (plan, payer, policy number) |
| `secondary_insurance` | Complete secondary insurance details |
| `tertiary_insurance` | Complete tertiary insurance details |
| `auto_accident_insurance` | Auto accident insurance information |
| `workers_comp_insurance` | Workers' compensation insurance details |
| `custom_demographics` | All custom demographic fields |
| `patient_flags` | Patient flag details and metadata |
| `patient_flags_attached` | Currently attached patient flags |
| `referring_doctor` | Complete referring physician information |

### Usage Examples

#### Get Single Patient with Insurance Details

```php
// Option 1: Using convenience method (recommended)
$patient = $client->patients->getWithInsurance(67890);

// Access insurance details
if (isset($patient['primary_insurance'])) {
    $insurance = $patient['primary_insurance'];
    echo "Primary Insurance:\n";
    echo "  Payer: {$insurance['insurance_payer_name']}\n";
    echo "  Plan: {$insurance['insurance_plan_name']}\n";
    echo "  Policy: {$insurance['insurance_id_number']}\n";
    echo "  Group: {$insurance['insurance_group_number']}\n";
}

// Access custom demographics
if (isset($patient['custom_demographics'])) {
    foreach ($patient['custom_demographics'] as $field) {
        echo "{$field['field_name']}: {$field['field_value']}\n";
    }
}

// Option 2: Using base verbose method
$patient = $client->patients->getVerbose(67890);
```

#### List Patients with Insurance Information

```php
// Get patients with insurance details
$patients = $client->patients->listWithInsurance([
    'date_of_birth' => '1980-05-15',
]);

foreach ($patients as $patient) {
    echo "Patient: {$patient['first_name']} {$patient['last_name']}\n";

    // Check insurance coverage
    $hasInsurance = isset($patient['primary_insurance']) && $patient['primary_insurance'];
    echo "Has Insurance: " . ($hasInsurance ? 'Yes' : 'No') . "\n";

    if ($hasInsurance) {
        echo "Primary Payer: {$patient['primary_insurance']['insurance_payer_name']}\n";
    }

    // Check for patient flags
    if (!empty($patient['patient_flags_attached'])) {
        echo "Flags: " . count($patient['patient_flags_attached']) . "\n";
    }
}
```

#### Search Patients by Insurance

```php
// Search for patients and include insurance details
$patients = $client->patients->search([
    'last_name' => 'Smith',
    'verbose' => 'true',
]);

foreach ($patients as $patient) {
    // Check if patient has workers comp
    if (isset($patient['workers_comp_insurance']) && $patient['workers_comp_insurance']) {
        echo "Workers Comp Case: {$patient['first_name']} {$patient['last_name']}\n";
        echo "Claim Number: {$patient['workers_comp_insurance']['claim_number']}\n";
    }
}
```

---

## Clinical Notes - Verbose Mode

### Additional Fields Included

When you use verbose mode with clinical notes, the following fields are included:

| Field | Description |
|-------|-------------|
| `clinical_note_sections` | Complete note sections with all field values and content |

### Usage Examples

#### Get Clinical Note with Full Sections

```php
// Option 1: Using convenience method (recommended)
$note = $client->clinicalNotes->getWithSections(54321);

// Access detailed sections
if (isset($note['clinical_note_sections'])) {
    foreach ($note['clinical_note_sections'] as $section) {
        echo "\nSection: {$section['section_name']}\n";
        echo "Content: {$section['section_content']}\n";

        // Access individual fields within section
        if (isset($section['fields'])) {
            foreach ($section['fields'] as $field) {
                echo "  {$field['field_name']}: {$field['field_value']}\n";
            }
        }
    }
}

// Option 2: Using base verbose method
$note = $client->clinicalNotes->getVerbose(54321);
```

#### List Clinical Notes by Patient with Sections

```php
// Get all clinical notes for a patient with full content
$notes = $client->clinicalNotes->listWithSections([
    'patient' => 98765,
    'since' => '2024-01-01',
]);

foreach ($notes as $note) {
    echo "Note ID: {$note['id']} - {$note['created_at']}\n";

    if (isset($note['clinical_note_sections'])) {
        echo "  Sections: " . count($note['clinical_note_sections']) . "\n";

        // Find specific section (e.g., Chief Complaint)
        foreach ($note['clinical_note_sections'] as $section) {
            if ($section['section_name'] === 'Chief Complaint') {
                echo "  Chief Complaint: {$section['section_content']}\n";
                break;
            }
        }
    }
}
```

#### Export Complete Clinical Documentation

```php
// Export all clinical notes for a patient with full content
$patient_id = 12345;
$notes = $client->clinicalNotes->listByPatient($patient_id, [
    'verbose' => 'true',
]);

$export = [];
foreach ($notes as $note) {
    $export[] = [
        'note_id' => $note['id'],
        'date' => $note['created_at'],
        'doctor' => $note['doctor'],
        'appointment' => $note['appointment'],
        'sections' => $note['clinical_note_sections'] ?? [],
    ];
}

// Save to JSON for external processing
file_put_contents("patient_{$patient_id}_notes.json", json_encode($export, JSON_PRETTY_PRINT));
```

---

## Low-Level Verbose Mode Access

All resources inherit verbose mode methods from `AbstractResource`. You can use these for any endpoint:

### Base Methods

```php
// Get single resource with verbose mode
$resource = $client->anyResource->getVerbose($id);

// List resources with verbose mode
$collection = $client->anyResource->listVerbose(['filter' => 'value']);

// Manual verbose mode (lowest level)
$collection = $client->anyResource->list(['verbose' => 'true']);
```

### Custom Filters with Verbose Mode

```php
// Combine verbose mode with other filters
$appointments = $client->appointments->listVerbose([
    'date' => '2024-03-15',
    'office' => 5,
    'status' => 'Complete',
]);

// Pagination with verbose mode
$patients = $client->patients->listVerbose([
    'page_size' => 25,  // Will be capped at 50 in verbose mode
]);

echo "Page size: {$patients->getPageSize()}\n";  // Will show 25
echo "Next page: {$patients->getNextUrl()}\n";
```

---

## Common Patterns

### Pattern 1: Conditional Verbose Mode

Only use verbose mode when needed:

```php
function getAppointmentDetails(int $id, bool $includeVitals = false): array
{
    $client = DrChronoClient::withAccessToken($_ENV['ACCESS_TOKEN']);

    if ($includeVitals) {
        return $client->appointments->getWithClinicalData($id);
    }

    return $client->appointments->get($id);
}

// Use verbose only when needed
$basic = getAppointmentDetails(123);  // Fast
$detailed = getAppointmentDetails(123, true);  // Slower but complete
```

### Pattern 2: Bulk Data Collection with Pagination

Efficiently collect verbose data across multiple pages:

```php
$allPatients = [];
$patients = $client->patients->listWithInsurance(['doctor' => 123]);

do {
    foreach ($patients as $patient) {
        // Only collect patients with insurance
        if (isset($patient['primary_insurance'])) {
            $allPatients[] = $patient;
        }
    }

    // Move to next page
    $patients = $patients->nextPage();
} while ($patients !== null);

echo "Found " . count($allPatients) . " patients with insurance\n";
```

### Pattern 3: Memory-Efficient Iteration

Use generators for large datasets:

```php
// Iterator automatically handles pagination
$appointments = $client->appointments->listWithClinicalData([
    'date_range' => '2024-01-01/2024-12-31',
]);

$totalWithNotes = 0;
foreach ($appointments as $appt) {
    if (isset($appt['clinical_note'])) {
        $totalWithNotes++;
    }

    // Memory is freed after each iteration
    unset($appt);
}

echo "Appointments with notes: $totalWithNotes\n";
```

### Pattern 4: Caching Verbose Results

Cache expensive verbose mode queries:

```php
use Psr\SimpleCache\CacheInterface;

class PatientService
{
    public function __construct(
        private DrChronoClient $client,
        private CacheInterface $cache
    ) {}

    public function getPatientWithInsurance(int $patientId): array
    {
        $cacheKey = "patient_insurance_{$patientId}";

        // Check cache first
        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Fetch with verbose mode
        $patient = $this->client->patients->getWithInsurance($patientId);

        // Cache for 1 hour
        $this->cache->set($cacheKey, $patient, 3600);

        return $patient;
    }
}
```

---

## Performance Optimization Tips

### 1. Use Verbose Mode Selectively

❌ **Don't do this:**
```php
// Fetching all patients with verbose mode - very slow!
$allPatients = $client->patients->listWithInsurance();
foreach ($allPatients as $patient) {
    echo $patient['first_name'];  // Only using basic field
}
```

✅ **Do this instead:**
```php
// Fetch basic data first
$allPatients = $client->patients->list();
foreach ($allPatients as $patient) {
    echo $patient['first_name'];
}

// Only fetch verbose data when needed
$patientWithInsurance = $client->patients->getWithInsurance($specificId);
```

### 2. Reduce Page Size for Verbose Queries

```php
// For verbose mode, smaller pages are better
$appointments = $client->appointments->listWithClinicalData([
    'page_size' => 20,  // Lower than default 50
    'date' => date('Y-m-d'),
]);
```

### 3. Filter Before Using Verbose Mode

```php
// BAD: Get everything then filter
$allAppointments = $client->appointments->listWithClinicalData();
$completed = array_filter($allAppointments->getItems(), fn($a) => $a['status'] === 'Complete');

// GOOD: Filter first, then get verbose
$completed = $client->appointments->listWithClinicalData([
    'status' => 'Complete',
    'date' => date('Y-m-d'),
]);
```

### 4. Parallelize Independent Verbose Requests

```php
// If you need verbose data for multiple resources, consider async requests
// (requires additional library like amphp/http-client or guzzle promises)

// Sequential (slow) - 3 seconds total
$patient = $client->patients->getWithInsurance(1);      // 1 second
$appointment = $client->appointments->getWithClinicalData(1);  // 1 second
$note = $client->clinicalNotes->getWithSections(1);     // 1 second

// With parallel requests - ~1 second total (pseudo-code)
$promises = [
    async(() => $client->patients->getWithInsurance(1)),
    async(() => $client->appointments->getWithClinicalData(1)),
    async(() => $client->clinicalNotes->getWithSections(1)),
];
[$patient, $appointment, $note] = await($promises);
```

---

## Troubleshooting

### Issue: Page size is still 250

**Problem**: You're not seeing the reduced page size in verbose mode.

**Solution**: Make sure you're actually using verbose mode:

```php
// This does NOT use verbose mode
$patients = $client->patients->list();

// This DOES use verbose mode
$patients = $client->patients->listWithInsurance();
// OR
$patients = $client->patients->listVerbose();
```

### Issue: Verbose fields are missing

**Problem**: Expected fields like `primary_insurance` are not in the response.

**Solution**:
1. Check that the patient actually has insurance data
2. Verify verbose mode is enabled
3. Check API permissions/scopes

```php
$patient = $client->patients->getWithInsurance(123);

if (!isset($patient['primary_insurance'])) {
    echo "Patient has no primary insurance on file\n";
} else {
    echo "Insurance: {$patient['primary_insurance']['insurance_payer_name']}\n";
}
```

### Issue: Slow performance

**Problem**: Verbose mode queries are very slow.

**Solution**:
1. Reduce page size: Use `page_size` parameter (max 50)
2. Add more filters to reduce result set
3. Cache results when possible
4. Use verbose mode only for detail views, not list views

```php
// Instead of this (slow):
$allPatients = $client->patients->listWithInsurance();

// Do this (faster):
$patients = $client->patients->list();  // Fast basic list
// Then get verbose data only for selected patient:
$selectedPatient = $client->patients->getWithInsurance($selectedId);
```

### Issue: Rate limit errors

**Problem**: Getting rate limited when using verbose mode.

**Solution**: The SDK automatically retries with exponential backoff, but you can also:

```php
use DrChrono\Exception\RateLimitException;

try {
    $patients = $client->patients->listWithInsurance(['doctor' => 123]);
} catch (RateLimitException $e) {
    // Wait for rate limit to reset
    $retryAfter = $e->getRetryAfter();
    echo "Rate limited. Retry after {$retryAfter} seconds\n";
    sleep($retryAfter);

    // Retry request
    $patients = $client->patients->listWithInsurance(['doctor' => 123]);
}
```

---

## API Reference

### AbstractResource Methods

All resources inherit these verbose mode methods:

#### `getVerbose(int|string $id): array`

Get a single resource with verbose mode enabled.

**Parameters:**
- `$id` - Resource ID

**Returns:** Array with verbose fields included

**Example:**
```php
$appointment = $client->appointments->getVerbose(12345);
```

#### `listVerbose(array $filters = []): PagedCollection`

List resources with verbose mode enabled.

**Parameters:**
- `$filters` - Optional query filters

**Returns:** PagedCollection with verbose data (max 50 per page)

**Example:**
```php
$patients = $client->patients->listVerbose(['doctor' => 123]);
```

### AppointmentsResource Methods

#### `getWithClinicalData(int $appointmentId): array`

Get appointment with clinical note, vitals, and status history.

**Verbose fields included:**
- `clinical_note`
- `vitals`
- `custom_vitals`
- `status_transitions`
- `reminders`
- `extended_updated_at`

#### `listWithClinicalData(array $filters = []): PagedCollection`

List appointments with clinical data.

### PatientsResource Methods

#### `getWithInsurance(int $patientId): array`

Get patient with full insurance details.

**Verbose fields included:**
- `primary_insurance`
- `secondary_insurance`
- `tertiary_insurance`
- `auto_accident_insurance`
- `workers_comp_insurance`
- `custom_demographics`
- `patient_flags`
- `patient_flags_attached`
- `referring_doctor`

#### `listWithInsurance(array $filters = []): PagedCollection`

List patients with insurance details.

### ClinicalNotesResource Methods

#### `getWithSections(int $noteId): array`

Get clinical note with complete section content.

**Verbose fields included:**
- `clinical_note_sections`

#### `listWithSections(array $filters = []): PagedCollection`

List clinical notes with section content.

---

## See Also

- [API Coverage Audit](API_COVERAGE_AUDIT.md) - Complete endpoint coverage details
- [Best Practices](BEST_PRACTICES.md) - General SDK best practices
- [Laravel Integration](LARAVEL_INTEGRATION.md) - Using verbose mode with Laravel
- [DrChrono API Documentation](https://app.drchrono.com/api-docs/) - Official API docs

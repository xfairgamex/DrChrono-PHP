# Implementation Guide - Complete API Coverage

This guide provides technical specifications and patterns for implementing complete DrChrono API coverage.

---

## Table of Contents

1. [Architecture Patterns](#architecture-patterns)
2. [Resource Implementation](#resource-implementation)
3. [Model Implementation](#model-implementation)
4. [Verbose Mode Support](#verbose-mode-support)
5. [Testing Strategy](#testing-strategy)
6. [Code Examples](#code-examples)

---

## Architecture Patterns

### Resource Class Pattern

All resource classes follow this pattern:

```php
<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * [Resource Name] - [Brief description]
 *
 * API Endpoint: /api/[endpoint]
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#[endpoint]
 */
class [Resource]Resource extends AbstractResource
{
    protected string $resourcePath = '/api/[endpoint]';

    /**
     * List [resources] with optional filters
     *
     * @param array $filters Optional filters:
     *   - 'verbose' (bool): Include additional fields (reduces page size to 50)
     *   - 'since' (string): Updated since timestamp
     *   - '[custom_filter]' (mixed): Resource-specific filter
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Custom convenience methods go here
     */
}
```

### Naming Conventions

#### Resource Classes
- **Pattern:** `{ResourceName}Resource`
- **Examples:**
  - `AppointmentProfilesResource` (plural endpoint)
  - `PatientPaymentsResource` (plural endpoint)
  - `EligibilityChecksResource` (plural endpoint)

#### Methods
- **List:** `list(array $filters = []): PagedCollection`
- **Get:** `get(int|string $id): array`
- **Create:** `create(array $data): array`
- **Update:** `update(int|string $id, array $data): array`
- **Delete:** `delete(int|string $id): void`
- **Custom:** Descriptive names like `listByPatient()`, `verify()`, `process()`

#### Models
- **Pattern:** Singular noun
- **Examples:** `AppointmentProfile`, `PatientPayment`, `EligibilityCheck`

---

## Resource Implementation

### Example: AppointmentProfilesResource

```php
<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Appointment Profiles - Manage appointment types with standard durations
 *
 * Appointment profiles define the types of appointments available in the practice,
 * including default duration, color coding, and scheduling rules.
 *
 * API Endpoint: /api/appointment_profiles
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#appointment_profiles
 */
class AppointmentProfilesResource extends AbstractResource
{
    protected string $resourcePath = '/api/appointment_profiles';

    /**
     * List appointment profiles
     *
     * @param array $filters Optional filters:
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'office' (int): Filter by office ID
     *   - 'archived' (bool): Include archived profiles
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get appointment profiles for a specific doctor
     *
     * @param int $doctorId Doctor ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByDoctor(int $doctorId, array $filters = []): PagedCollection
    {
        $filters['doctor'] = $doctorId;
        return $this->list($filters);
    }

    /**
     * Get appointment profiles for a specific office
     *
     * @param int $officeId Office ID
     * @param array $filters Additional filters
     * @return PagedCollection
     */
    public function listByOffice(int $officeId, array $filters = []): PagedCollection
    {
        $filters['office'] = $officeId;
        return $this->list($filters);
    }

    /**
     * Create appointment profile
     *
     * Required fields:
     * - name (string): Profile name
     * - duration (int): Duration in minutes
     * - doctor (int): Doctor ID
     *
     * @param array $data Profile data
     * @return array Created profile
     */
    public function createProfile(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update appointment profile
     *
     * @param int $profileId Profile ID
     * @param array $data Updated data
     * @return array Updated profile
     */
    public function updateProfile(int $profileId, array $data): array
    {
        return $this->update($profileId, $data);
    }

    /**
     * Archive appointment profile
     *
     * @param int $profileId Profile ID
     * @return array Archived profile
     */
    public function archive(int $profileId): array
    {
        return $this->update($profileId, ['archived' => true]);
    }
}
```

### Example: BillingProfilesResource

```php
<?php

declare(strict_types=1);

namespace DrChrono\Resource;

/**
 * Billing Profiles - Manage billing configurations
 *
 * Billing profiles contain settings for billing, claims, and payment processing
 * including tax IDs, billing addresses, and claim defaults.
 *
 * API Endpoint: /api/billing_profiles
 * Documentation: https://app.drchrono.com/api-docs-old/v4/documentation#billing_profiles
 */
class BillingProfilesResource extends AbstractResource
{
    protected string $resourcePath = '/api/billing_profiles';

    /**
     * List billing profiles
     *
     * @param array $filters Optional filters:
     *   - 'doctor' (int): Filter by doctor ID
     *   - 'practice' (int): Filter by practice ID
     * @return PagedCollection
     */
    public function list(array $filters = []): PagedCollection
    {
        return parent::list($filters);
    }

    /**
     * Get billing profile for a specific doctor
     *
     * @param int $doctorId Doctor ID
     * @return array|null Billing profile or null if not found
     */
    public function getByDoctor(int $doctorId): ?array
    {
        $profiles = $this->list(['doctor' => $doctorId]);
        $items = $profiles->getItems();
        return !empty($items) ? $items[0] : null;
    }

    /**
     * Create billing profile
     *
     * Required fields vary by practice setup
     *
     * @param array $data Profile data
     * @return array Created profile
     */
    public function createProfile(array $data): array
    {
        return $this->create($data);
    }

    /**
     * Update billing profile
     *
     * @param int $profileId Profile ID
     * @param array $data Updated data
     * @return array Updated profile
     */
    public function updateProfile(int $profileId, array $data): array
    {
        return $this->update($profileId, $data);
    }
}
```

---

## Model Implementation

### Base Model Pattern

```php
<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * [Model Name] Model
 */
class [ModelName] extends AbstractModel
{
    /**
     * Get [field description]
     */
    public function get[FieldName](): ?[Type]
    {
        return $this->data['field_name'] ?? null;
    }

    /**
     * Set [field description]
     */
    public function set[FieldName]([Type] $value): self
    {
        $this->data['field_name'] = $value;
        return $this;
    }

    /**
     * Check if [condition]
     */
    public function is[Condition](): bool
    {
        return $this->data['field'] === 'value';
    }
}
```

### Example: AppointmentProfile Model

```php
<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Appointment Profile Model
 *
 * Represents an appointment type with default duration and settings
 */
class AppointmentProfile extends AbstractModel
{
    /**
     * Get profile ID
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get profile name
     */
    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    /**
     * Set profile name
     */
    public function setName(string $name): self
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * Get duration in minutes
     */
    public function getDuration(): ?int
    {
        return $this->data['duration'] ?? null;
    }

    /**
     * Set duration in minutes
     */
    public function setDuration(int $duration): self
    {
        $this->data['duration'] = $duration;
        return $this;
    }

    /**
     * Get doctor ID
     */
    public function getDoctorId(): ?int
    {
        return $this->data['doctor'] ?? null;
    }

    /**
     * Set doctor ID
     */
    public function setDoctor(int $doctorId): self
    {
        $this->data['doctor'] = $doctorId;
        return $this;
    }

    /**
     * Get color code for calendar display
     */
    public function getColor(): ?string
    {
        return $this->data['color'] ?? null;
    }

    /**
     * Set color code (hex format)
     */
    public function setColor(string $color): self
    {
        $this->data['color'] = $color;
        return $this;
    }

    /**
     * Check if profile is archived
     */
    public function isArchived(): bool
    {
        return $this->data['archived'] ?? false;
    }

    /**
     * Set archived status
     */
    public function setArchived(bool $archived): self
    {
        $this->data['archived'] = $archived;
        return $this;
    }

    /**
     * Check if profile is online bookable
     */
    public function isOnlineBookable(): bool
    {
        return $this->data['online_scheduling_enabled'] ?? false;
    }

    /**
     * Get sorting order
     */
    public function getSortOrder(): ?int
    {
        return $this->data['sort_order'] ?? null;
    }
}
```

### Example: BillingProfile Model

```php
<?php

declare(strict_types=1);

namespace DrChrono\Model;

/**
 * Billing Profile Model
 *
 * Represents billing configuration for a provider or practice
 */
class BillingProfile extends AbstractModel
{
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    public function getDoctorId(): ?int
    {
        return $this->data['doctor'] ?? null;
    }

    public function setDoctor(int $doctorId): self
    {
        $this->data['doctor'] = $doctorId;
        return $this;
    }

    public function getTaxId(): ?string
    {
        return $this->data['tax_id'] ?? null;
    }

    public function setTaxId(string $taxId): self
    {
        $this->data['tax_id'] = $taxId;
        return $this;
    }

    public function getNpi(): ?string
    {
        return $this->data['npi'] ?? null;
    }

    public function setNpi(string $npi): self
    {
        $this->data['npi'] = $npi;
        return $this;
    }

    public function getBillingAddress(): ?array
    {
        return $this->data['billing_address'] ?? null;
    }

    public function setBillingAddress(array $address): self
    {
        $this->data['billing_address'] = $address;
        return $this;
    }

    public function getPayToAddress(): ?array
    {
        return $this->data['pay_to_address'] ?? null;
    }

    public function setPayToAddress(array $address): self
    {
        $this->data['pay_to_address'] = $address;
        return $this;
    }
}
```

---

## Verbose Mode Support

### Update AbstractResource

Add verbose mode helper to `src/Resource/AbstractResource.php`:

```php
/**
 * Enable verbose mode for the next request
 * Returns additional fields but reduces page size to 50
 */
protected function withVerbose(array $filters): array
{
    $filters['verbose'] = 'true';
    return $filters;
}

/**
 * List resources with verbose mode enabled
 *
 * Verbose mode includes additional fields but may reduce performance:
 * - Page size limited to 50 (vs 250 default)
 * - Additional database queries per record
 *
 * See API docs for which fields require verbose mode
 */
public function listVerbose(array $filters = []): PagedCollection
{
    return $this->list($this->withVerbose($filters));
}

/**
 * Get a single resource with verbose mode
 */
public function getVerbose(int|string $id): array
{
    return $this->httpClient->get("{$this->resourcePath}/{$id}", ['verbose' => 'true']);
}
```

### Update PatientsResource

```php
/**
 * Get patient with full insurance details
 *
 * Requires verbose mode to include:
 * - primary_insurance (full details)
 * - secondary_insurance (full details)
 * - tertiary_insurance (full details)
 * - auto_accident_insurance
 * - workers_comp_insurance
 * - custom_demographics
 * - patient_flags
 * - referring_doctor
 */
public function getWithInsurance(int $patientId): array
{
    return $this->getVerbose($patientId);
}

/**
 * List patients with insurance details
 */
public function listWithInsurance(array $filters = []): PagedCollection
{
    return $this->listVerbose($filters);
}
```

### Update AppointmentsResource

```php
/**
 * Get appointment with clinical details
 *
 * Requires verbose mode to include:
 * - clinical_note (associated note)
 * - vitals (vital signs)
 * - custom_vitals (custom vitals)
 * - status_transitions (status history)
 * - reminders (reminder settings)
 * - extended_updated_at
 */
public function getWithClinicalData(int $appointmentId): array
{
    return $this->getVerbose($appointmentId);
}

/**
 * List appointments with clinical data
 */
public function listWithClinicalData(array $filters = []): PagedCollection
{
    return $this->listVerbose($filters);
}
```

### Documentation

Create `docs/VERBOSE_MODE.md`:

```markdown
# Verbose Mode Guide

## Overview

Verbose mode (`verbose=true`) includes additional fields that require extra database queries or processing. This increases response detail but reduces performance and limits page size to 50 records.

## Endpoints Supporting Verbose Mode

### Appointments

**Additional fields with verbose=true:**
- `clinical_note` - Full clinical note object
- `vitals` - Array of vital sign measurements
- `custom_vitals` - Array of custom vital measurements
- `status_transitions` - Array of status change history
- `reminders` - Reminder configuration object
- `extended_updated_at` - Extended timestamp data

**Usage:**
```php
// Method 1: Using helper method
$appointment = $client->appointments->getWithClinicalData($id);

// Method 2: Manual verbose flag
$appointments = $client->appointments->list(['verbose' => 'true']);
```

### Patients

**Additional fields with verbose=true:**
- `primary_insurance` - Complete insurance object
- `secondary_insurance` - Complete insurance object
- `tertiary_insurance` - Complete insurance object
- `auto_accident_insurance` - Auto insurance details
- `workers_comp_insurance` - Workers comp details
- `custom_demographics` - Custom field values
- `patient_flags` - Array of patient flags
- `patient_flags_attached` - Attached flag details
- `referring_doctor` - Referring physician details

**Usage:**
```php
// Get patient with all insurance details
$patient = $client->patients->getWithInsurance($patientId);
```

### Clinical Notes

**Additional fields with verbose=true:**
- `clinical_note_sections` - Full section content array

## Performance Considerations

- **Page size:** Reduced from 250 to 50 with verbose mode
- **Response time:** 2-5x slower per request
- **Use when:** You need the additional fields
- **Avoid when:** Listing large datasets or performance is critical

## Best Practices

1. **Use selectively:** Only enable verbose mode when you need the extra fields
2. **Cache results:** Consider caching verbose responses
3. **Paginate wisely:** Account for the smaller page size
4. **Filter first:** Apply filters before enabling verbose mode
```

---

## Testing Strategy

### Unit Test Template

```php
<?php

declare(strict_types=1);

namespace DrChrono\Tests\Resource;

use DrChrono\Resource\[Resource]Resource;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\PagedCollection;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class [Resource]ResourceTest extends TestCase
{
    private [Resource]Resource $resource;
    private HttpClient|MockObject $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClient::class);
        $this->resource = new [Resource]Resource($this->httpClient);
    }

    public function testList(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Test 1'],
                ['id' => 2, 'name' => 'Test 2'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/[endpoint]', [])
            ->willReturn($expectedResponse);

        $result = $this->resource->list();

        $this->assertInstanceOf(PagedCollection::class, $result);
        $this->assertCount(2, $result->getItems());
    }

    public function testGet(): void
    {
        $expectedResponse = ['id' => 1, 'name' => 'Test'];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/[endpoint]/1')
            ->willReturn($expectedResponse);

        $result = $this->resource->get(1);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testCreate(): void
    {
        $data = ['name' => 'New Item'];
        $expectedResponse = ['id' => 1, 'name' => 'New Item'];

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->with('/api/[endpoint]', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->create($data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testUpdate(): void
    {
        $data = ['name' => 'Updated Item'];
        $expectedResponse = ['id' => 1, 'name' => 'Updated Item'];

        $this->httpClient
            ->expects($this->once())
            ->method('patch')
            ->with('/api/[endpoint]/1', $data)
            ->willReturn($expectedResponse);

        $result = $this->resource->update(1, $data);

        $this->assertEquals($expectedResponse, $result);
    }

    public function testDelete(): void
    {
        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('/api/[endpoint]/1')
            ->willReturn([]);

        $this->resource->delete(1);
    }

    public function testListVerbose(): void
    {
        $expectedResponse = [
            'results' => [
                ['id' => 1, 'name' => 'Test', 'extra_field' => 'data'],
            ],
            'next' => null,
            'previous' => null,
        ];

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('/api/[endpoint]', ['verbose' => 'true'])
            ->willReturn($expectedResponse);

        $result = $this->resource->listVerbose();

        $this->assertInstanceOf(PagedCollection::class, $result);
    }
}
```

### Model Test Template

```php
<?php

declare(strict_types=1);

namespace DrChrono\Tests\Model;

use DrChrono\Model\[Model];
use PHPUnit\Framework\TestCase;

class [Model]Test extends TestCase
{
    public function testFromArray(): void
    {
        $data = [
            'id' => 1,
            'name' => 'Test',
            'field' => 'value',
        ];

        $model = [Model]::fromArray($data);

        $this->assertEquals(1, $model->getId());
        $this->assertEquals('Test', $model->getName());
        $this->assertEquals('value', $model->getField());
    }

    public function testToArray(): void
    {
        $model = new [Model]();
        $model->setName('Test')
              ->setField('value');

        $array = $model->toArray();

        $this->assertEquals('Test', $array['name']);
        $this->assertEquals('value', $array['field']);
    }

    public function testChaining(): void
    {
        $model = (new [Model]())
            ->setName('Test')
            ->setField('value');

        $this->assertEquals('Test', $model->getName());
        $this->assertEquals('value', $model->getField());
    }

    public function testNullDefaults(): void
    {
        $model = new [Model]();

        $this->assertNull($model->getId());
        $this->assertNull($model->getName());
    }
}
```

---

## Code Examples

### Example 1: Using New Resources

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;

$client = DrChronoClient::withAccessToken($_ENV['DRCHRONO_ACCESS_TOKEN']);

// List appointment profiles
$profiles = $client->appointmentProfiles->listByDoctor(123456);

foreach ($profiles as $profile) {
    echo "{$profile['name']} - {$profile['duration']} minutes\n";
}

// Create new appointment profile
$newProfile = $client->appointmentProfiles->createProfile([
    'name' => 'Telehealth Visit',
    'duration' => 20,
    'doctor' => 123456,
    'color' => '#4A90E2',
    'online_scheduling_enabled' => true,
]);

echo "Created profile: {$newProfile['id']}\n";
```

### Example 2: Verbose Mode Usage

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;

$client = DrChronoClient::withAccessToken($_ENV['DRCHRONO_ACCESS_TOKEN']);

// Get patient with full insurance details
$patient = $client->patients->getWithInsurance(789012);

if (isset($patient['primary_insurance'])) {
    $insurance = $patient['primary_insurance'];
    echo "Insurance: {$insurance['insurance_company']}\n";
    echo "Member ID: {$insurance['insurance_id_number']}\n";
    echo "Group: {$insurance['insurance_group_number']}\n";
}

// Get appointment with clinical data
$appointment = $client->appointments->getWithClinicalData(456789);

if (isset($appointment['vitals'])) {
    foreach ($appointment['vitals'] as $vital) {
        echo "{$vital['type']}: {$vital['value']}\n";
    }
}

if (isset($appointment['clinical_note'])) {
    echo "Note ID: {$appointment['clinical_note']['id']}\n";
}
```

### Example 3: Billing Workflow

```php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use DrChrono\DrChronoClient;

$client = DrChronoClient::withAccessToken($_ENV['DRCHRONO_ACCESS_TOKEN']);

// Get billing profile for provider
$billingProfile = $client->billingProfiles->getByDoctor(123456);

echo "Billing NPI: {$billingProfile['npi']}\n";
echo "Tax ID: {$billingProfile['tax_id']}\n";

// Check insurance eligibility
$eligibility = $client->eligibilityChecks->verify([
    'patient' => 789012,
    'appointment' => 456789,
    'insurance' => 'primary',
]);

if ($eligibility['is_eligible']) {
    echo "Patient is eligible\n";
    echo "Copay: \${$eligibility['copay_amount']}\n";
} else {
    echo "Eligibility check failed: {$eligibility['error_message']}\n";
}

// Create transaction
$transaction = $client->transactions->create([
    'patient' => 789012,
    'appointment' => 456789,
    'amount' => 50.00,
    'payment_method' => 'Credit Card',
    'transaction_type' => 'Payment',
]);

echo "Transaction created: {$transaction['id']}\n";
```

---

## Checklist for Each New Resource

- [ ] Create Resource class in `src/Resource/`
- [ ] Add PHPDoc with API endpoint and documentation link
- [ ] Implement standard CRUD methods
- [ ] Add convenience methods (listBy*, custom operations)
- [ ] Add verbose mode support if applicable
- [ ] Create Model class in `src/Model/`
- [ ] Register resource in `DrChronoClient::getResource()`
- [ ] Add `@property-read` to DrChronoClient PHPDoc
- [ ] Create unit tests
- [ ] Update README resource list
- [ ] Add usage example
- [ ] Update CHANGELOG

---

## Quality Standards

### Code Style
- PSR-12 compliant
- PHPStan level 8
- PHP-CS-Fixer with project rules

### Documentation
- PHPDoc for all public methods
- Parameter descriptions with types
- Return type documentation
- Usage examples in docblocks

### Testing
- 90%+ code coverage
- Unit tests for all methods
- Edge case testing
- Mock external API calls

---

**Next:** Begin Phase 1 implementation following these patterns

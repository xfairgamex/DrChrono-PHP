# DrChrono PHP SDK - Best Practices Guide

This guide covers production-ready patterns, performance optimization, security considerations, and common pitfalls when using the DrChrono PHP SDK.

---

## Table of Contents

1. [Authentication & Security](#authentication--security)
2. [Performance Optimization](#performance-optimization)
3. [Error Handling](#error-handling)
4. [Pagination Strategies](#pagination-strategies)
5. [Rate Limiting](#rate-limiting)
6. [Caching Strategies](#caching-strategies)
7. [Webhook Handling](#webhook-handling)
8. [Testing & Debugging](#testing--debugging)
9. [Production Deployment](#production-deployment)
10. [Common Pitfalls](#common-pitfalls)

---

## Authentication & Security

### Token Management

**✅ DO: Store tokens securely**

```php
// Use environment variables or secure vaults
$client = DrChronoClient::withAccessToken($_ENV['DRCHRONO_ACCESS_TOKEN']);

// Or use encrypted storage
$encryptedToken = decrypt($user->drchrono_token);
$client = DrChronoClient::withAccessToken($encryptedToken);
```

**❌ DON'T: Hard-code credentials**

```php
// NEVER do this!
$client = DrChronoClient::withAccessToken('abc123xyz');
```

### Automatic Token Refresh

**✅ DO: Implement automatic token refresh**

```php
class DrChronoService
{
    private DrChronoClient $client;

    public function __construct()
    {
        $this->client = DrChronoClient::withOAuth(
            clientId: $_ENV['DRCHRONO_CLIENT_ID'],
            clientSecret: $_ENV['DRCHRONO_CLIENT_SECRET'],
            redirectUri: $_ENV['DRCHRONO_REDIRECT_URI']
        );

        // Set existing tokens
        $config = $this->client->getConfig();
        $config->setAccessToken($this->getStoredAccessToken());
        $config->setRefreshToken($this->getStoredRefreshToken());
        $config->setTokenExpiry($this->getStoredTokenExpiry());
    }

    public function makeRequest(callable $callback)
    {
        // Ensure token is valid before making request
        if ($this->client->getConfig()->isTokenExpired()) {
            $this->refreshToken();
        }

        return $callback($this->client);
    }

    private function refreshToken(): void
    {
        $tokens = $this->client->auth()->refreshAccessToken();

        // Store new tokens securely
        $this->storeAccessToken($tokens['access_token']);
        $this->storeRefreshToken($tokens['refresh_token']);
        $this->storeTokenExpiry($tokens['expires_at']);

        // Update client config
        $this->client->getConfig()->setAccessToken($tokens['access_token']);
    }

    // Implement these methods based on your storage mechanism
    private function getStoredAccessToken(): string { /* ... */ }
    private function getStoredRefreshToken(): string { /* ... */ }
    private function getStoredTokenExpiry(): int { /* ... */ }
    private function storeAccessToken(string $token): void { /* ... */ }
    private function storeRefreshToken(string $token): void { /* ... */ }
    private function storeTokenExpiry(int $expiry): void { /* ... */ }
}
```

### OAuth Scope Management

**✅ DO: Request only necessary scopes**

```php
// Request minimal scopes needed
$authUrl = $client->auth()->getAuthorizationUrl([
    'patients:read',
    'appointments:read',
    'appointments:write'
]);
```

**❌ DON'T: Request all scopes unnecessarily**

```php
// Avoid requesting broad access when not needed
$authUrl = $client->auth()->getAuthorizationUrl(['user/*:*']);
```

---

## Performance Optimization

### Use Pagination Wisely

**✅ DO: Use iterators for large datasets**

```php
// Memory-efficient iteration through all patients
foreach ($client->patients->iterateAll() as $patient) {
    // Process one patient at a time
    processPatient($patient);
}
```

**❌ DON'T: Load all records into memory**

```php
// This can cause memory issues with large datasets
$allPatients = $client->patients->all();
foreach ($allPatients as $patient) {
    processPatient($patient);
}
```

### Optimize Verbose Mode Usage

**✅ DO: Use verbose mode selectively**

```php
// Use verbose mode only when you need the extra fields
$patientId = 12345;

// Get basic patient data (fast)
$patient = $client->patients->get($patientId);

// Only fetch insurance details if needed
if ($needsInsuranceInfo) {
    $patientWithInsurance = $client->patients->getWithInsurance($patientId);
}
```

**❌ DON'T: Always use verbose mode**

```php
// Verbose mode reduces page size from 250 to 50 and is 2-5x slower
$patients = $client->patients->listWithInsurance(); // Slow for large lists
```

### Filter Before Fetching

**✅ DO: Apply server-side filters**

```php
// Let the API filter data (much faster)
$appointments = $client->appointments->listByDateRange(
    startDate: '2025-01-01',
    endDate: '2025-01-31',
    filters: ['doctor' => 123, 'status' => 'Scheduled']
);
```

**❌ DON'T: Fetch all and filter in PHP**

```php
// Inefficient - fetches all appointments then filters
$allAppointments = $client->appointments->iterateAll();
$filtered = array_filter($allAppointments, function($apt) {
    return $apt['status'] === 'Scheduled';
});
```

### Batch Similar Operations

**✅ DO: Group related API calls**

```php
// Fetch multiple related resources together
$patientId = 12345;

// Use parallel calls if your framework supports it
$patient = $client->patients->get($patientId);
$allergies = $client->allergies->listByPatient($patientId);
$medications = $client->medications->listByPatient($patientId);
$problems = $client->problems->listByPatient($patientId);
```

---

## Error Handling

### Graceful Degradation

**✅ DO: Handle errors gracefully**

```php
use DrChrono\Exception\{ApiException, RateLimitException, ValidationException};

function getPatientSafely(int $patientId): ?array
{
    try {
        return $this->client->patients->get($patientId);
    } catch (ValidationException $e) {
        // Log validation errors for debugging
        logger()->warning('Invalid patient ID', [
            'patient_id' => $patientId,
            'errors' => $e->getValidationErrors()
        ]);
        return null;
    } catch (RateLimitException $e) {
        // Wait and retry
        sleep($e->getRetryAfter());
        return $this->client->patients->get($patientId);
    } catch (ApiException $e) {
        // Log and return null
        logger()->error('DrChrono API error', [
            'patient_id' => $patientId,
            'status' => $e->getStatusCode(),
            'message' => $e->getMessage()
        ]);
        return null;
    }
}
```

### Retry Logic for Transient Failures

**✅ DO: Implement exponential backoff**

```php
function makeRequestWithRetry(callable $request, int $maxRetries = 3): mixed
{
    $attempt = 0;
    $delay = 1000; // Start with 1 second

    while ($attempt < $maxRetries) {
        try {
            return $request();
        } catch (RateLimitException $e) {
            // Use the API's suggested retry time
            usleep($e->getRetryAfter() * 1000000);
            $attempt++;
        } catch (ApiException $e) {
            // Only retry on 5xx errors
            if ($e->getStatusCode() >= 500) {
                if ($attempt >= $maxRetries - 1) {
                    throw $e;
                }
                usleep($delay * 1000);
                $delay *= 2; // Exponential backoff
                $attempt++;
            } else {
                throw $e; // Don't retry client errors (4xx)
            }
        }
    }

    throw new \RuntimeException('Max retries exceeded');
}

// Usage
$patient = makeRequestWithRetry(
    fn() => $client->patients->get(12345)
);
```

### Log API Errors

**✅ DO: Log errors with context**

```php
try {
    $appointment = $client->appointments->create($data);
} catch (ValidationException $e) {
    logger()->error('Appointment creation failed', [
        'errors' => $e->getValidationErrors(),
        'data' => $data,
        'user_id' => auth()->id(),
        'timestamp' => now()
    ]);
    throw $e;
}
```

---

## Pagination Strategies

### Choose the Right Pagination Method

**For Background Jobs (Memory Efficient)**

```php
// Process large datasets in batches
foreach ($client->patients->iterateAll() as $patient) {
    // Memory efficient - only one page in memory at a time
    queue()->push(new ProcessPatientJob($patient));
}
```

**For API Responses (Paginated)**

```php
// Return paginated results to frontend
public function index(Request $request)
{
    $page = $request->get('page', 1);
    $pageSize = $request->get('page_size', 50);

    $patients = $client->patients->list([
        'page' => $page,
        'page_size' => $pageSize,
        'doctor' => $request->get('doctor_id')
    ]);

    return response()->json([
        'data' => $patients->getItems(),
        'pagination' => [
            'current_page' => $page,
            'per_page' => $pageSize,
            'has_next' => $patients->hasNext(),
            'has_previous' => $patients->hasPrevious(),
            'total_count' => $patients->count()
        ]
    ]);
}
```

**For Reports (All Data)**

```php
// Generate reports with all data
public function generateReport()
{
    $allAppointments = $client->appointments->all([
        'since' => now()->subMonths(3)->toDateString(),
        'doctor' => 123
    ]);

    return Excel::download(
        new AppointmentsExport($allAppointments),
        'appointments.xlsx'
    );
}
```

---

## Rate Limiting

### The SDK Handles Rate Limits Automatically

The SDK automatically retries rate-limited requests with exponential backoff. However, you can optimize to avoid hitting limits:

**✅ DO: Implement application-level rate limiting**

```php
use Illuminate\Support\Facades\RateLimiter;

class DrChronoService
{
    public function makeThrottledRequest(string $key, callable $callback)
    {
        // Limit to 4 requests per second (API limit is ~5/sec)
        $executed = RateLimiter::attempt(
            "drchrono:{$key}",
            $perSecond = 4,
            $callback
        );

        if (!$executed) {
            throw new \RuntimeException('Rate limit exceeded');
        }

        return $executed;
    }
}

// Usage
$service = new DrChronoService();
$patient = $service->makeThrottledRequest(
    'patients',
    fn() => $client->patients->get(12345)
);
```

### Monitor API Usage

**✅ DO: Track API calls**

```php
class DrChronoMonitor
{
    public function trackApiCall(string $endpoint, float $duration): void
    {
        // Log to monitoring service
        Metrics::increment('drchrono.api.calls', [
            'endpoint' => $endpoint
        ]);

        Metrics::timing('drchrono.api.duration', $duration, [
            'endpoint' => $endpoint
        ]);
    }
}
```

---

## Caching Strategies

### Cache Reference Data

**✅ DO: Cache data that rarely changes**

```php
use Illuminate\Support\Facades\Cache;

class DrChronoService
{
    public function getDoctors(): array
    {
        // Doctors list rarely changes - cache for 24 hours
        return Cache::remember('drchrono:doctors', 86400, function() {
            return $this->client->doctors->list()->getItems();
        });
    }

    public function getOffices(): array
    {
        // Office list rarely changes - cache for 12 hours
        return Cache::remember('drchrono:offices', 43200, function() {
            return $this->client->offices->listAll();
        });
    }

    public function getAppointmentProfiles(int $doctorId): array
    {
        // Appointment profiles change occasionally - cache for 1 hour
        return Cache::remember(
            "drchrono:appointment_profiles:{$doctorId}",
            3600,
            function() use ($doctorId) {
                return $this->client->appointmentProfiles
                    ->listByDoctor($doctorId)
                    ->getItems();
            }
        );
    }
}
```

### Cache Patient Data Carefully

**⚠️ CAUTION: Patient data is sensitive**

```php
// Cache patient data for short periods only
public function getPatient(int $patientId): array
{
    // Cache for 5 minutes only
    return Cache::remember("drchrono:patient:{$patientId}", 300, function() use ($patientId) {
        return $this->client->patients->get($patientId);
    });
}

// Invalidate cache when patient data changes
public function updatePatient(int $patientId, array $data): array
{
    $patient = $this->client->patients->update($patientId, $data);

    // Invalidate cache
    Cache::forget("drchrono:patient:{$patientId}");

    return $patient;
}
```

**❌ DON'T: Cache sensitive data for long periods**

```php
// Bad - patient data cached for 24 hours
Cache::remember("patient:{$id}", 86400, fn() => $client->patients->get($id));
```

### Use Cache Tags

**✅ DO: Group related cache entries**

```php
// Group all patient-related caches
Cache::tags(['patient', "patient:{$patientId}"])->put(
    "patient:{$patientId}:appointments",
    $appointments,
    300
);

// Invalidate all patient caches
Cache::tags("patient:{$patientId}")->flush();
```

---

## Webhook Handling

### Verify All Webhooks

**✅ DO: Always verify webhook signatures**

```php
use DrChrono\Webhook\WebhookVerifier;

public function handleWebhook(Request $request)
{
    $verifier = new WebhookVerifier($_ENV['DRCHRONO_CLIENT_SECRET']);

    try {
        $event = $verifier->verifyFromRequest(
            $request->getContent(),
            $request->headers->all()
        );
    } catch (\DrChrono\Exception\WebhookVerificationException $e) {
        logger()->warning('Invalid webhook signature', [
            'ip' => $request->ip(),
            'headers' => $request->headers->all()
        ]);
        return response('Unauthorized', 401);
    }

    // Process valid webhook
    $this->processWebhookEvent($event);

    return response('OK', 200);
}
```

### Handle Webhooks Asynchronously

**✅ DO: Queue webhook processing**

```php
public function handleWebhook(Request $request)
{
    $verifier = new WebhookVerifier($_ENV['DRCHRONO_CLIENT_SECRET']);

    try {
        $event = $verifier->verifyFromRequest(
            $request->getContent(),
            $request->headers->all()
        );

        // Queue for async processing
        dispatch(new ProcessDrChronoWebhookJob($event->toArray()));

        // Return success immediately
        return response('OK', 200);

    } catch (\Exception $e) {
        logger()->error('Webhook processing failed', [
            'error' => $e->getMessage()
        ]);
        return response('Error', 500);
    }
}
```

### Implement Idempotency

**✅ DO: Handle duplicate webhooks**

```php
public function processWebhook(WebhookEvent $event): void
{
    $hookId = $event->getHookId();

    // Check if already processed
    if (Cache::has("webhook:processed:{$hookId}")) {
        logger()->info('Duplicate webhook ignored', ['hook_id' => $hookId]);
        return;
    }

    // Process webhook
    if ($event->is('appointment.created')) {
        $this->handleAppointmentCreated($event);
    }

    // Mark as processed (store for 24 hours)
    Cache::put("webhook:processed:{$hookId}", true, 86400);
}
```

---

## Testing & Debugging

### Use Mocks in Tests

**✅ DO: Mock API responses**

```php
use DrChrono\Client\HttpClient;
use PHPUnit\Framework\TestCase;

class PatientServiceTest extends TestCase
{
    public function test_can_fetch_patient()
    {
        // Mock the HTTP client
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->method('get')
            ->with('/api/patients/123')
            ->willReturn([
                'id' => 123,
                'first_name' => 'John',
                'last_name' => 'Doe'
            ]);

        $client = new DrChronoClient($config);
        $client->setHttpClient($httpClient);

        $patient = $client->patients->get(123);

        $this->assertEquals('John', $patient['first_name']);
    }
}
```

### Enable Debug Mode in Development

**✅ DO: Use debug mode for troubleshooting**

```php
use DrChrono\Client\Config;

$config = new Config([
    'access_token' => $_ENV['DRCHRONO_ACCESS_TOKEN'],
    'debug' => $_ENV['APP_ENV'] === 'local', // Only in dev
]);

$client = new DrChronoClient($config);
```

### Log API Requests in Development

**✅ DO: Create a logging wrapper**

```php
class DrChronoLogger
{
    private DrChronoClient $client;

    public function logRequest(string $method, string $endpoint, array $data = []): mixed
    {
        $start = microtime(true);

        try {
            $result = match($method) {
                'GET' => $this->client->getHttpClient()->get($endpoint),
                'POST' => $this->client->getHttpClient()->post($endpoint, $data),
                'PATCH' => $this->client->getHttpClient()->patch($endpoint, $data),
                'DELETE' => $this->client->getHttpClient()->delete($endpoint),
            };

            $duration = microtime(true) - $start;

            logger()->info('DrChrono API Request', [
                'method' => $method,
                'endpoint' => $endpoint,
                'duration' => round($duration, 3),
                'status' => 'success'
            ]);

            return $result;

        } catch (\Exception $e) {
            $duration = microtime(true) - $start;

            logger()->error('DrChrono API Request Failed', [
                'method' => $method,
                'endpoint' => $endpoint,
                'duration' => round($duration, 3),
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}
```

---

## Production Deployment

### Environment Configuration

**✅ DO: Use environment-specific configs**

```php
// .env.production
DRCHRONO_CLIENT_ID=your_production_client_id
DRCHRONO_CLIENT_SECRET=your_production_secret
DRCHRONO_API_BASE_URL=https://app.drchrono.com
DRCHRONO_TIMEOUT=60
DRCHRONO_MAX_RETRIES=3
DRCHRONO_DEBUG=false

// .env.local
DRCHRONO_CLIENT_ID=your_sandbox_client_id
DRCHRONO_CLIENT_SECRET=your_sandbox_secret
DRCHRONO_API_BASE_URL=https://app.drchrono.com
DRCHRONO_DEBUG=true
```

### Health Checks

**✅ DO: Implement API health checks**

```php
class DrChronoHealthCheck
{
    public function check(): array
    {
        try {
            $start = microtime(true);
            $user = $this->client->users->getCurrent();
            $duration = microtime(true) - $start;

            return [
                'status' => 'healthy',
                'response_time' => round($duration, 3),
                'user_id' => $user['id'] ?? null
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
        }
    }
}
```

### Monitor API Performance

**✅ DO: Track API metrics**

```php
// Track key metrics
class DrChronoMetrics
{
    public function recordApiCall(string $resource, float $duration, bool $success): void
    {
        Metrics::increment('drchrono.api.calls.total', [
            'resource' => $resource,
            'status' => $success ? 'success' : 'failure'
        ]);

        Metrics::histogram('drchrono.api.response_time', $duration, [
            'resource' => $resource
        ]);
    }

    public function recordCacheHit(string $key): void
    {
        Metrics::increment('drchrono.cache.hits', [
            'key_prefix' => explode(':', $key)[0]
        ]);
    }
}
```

---

## Common Pitfalls

### ❌ PITFALL: Not handling token expiration

```php
// Bad - token expires during long-running job
foreach ($largeDataset as $item) {
    $patient = $client->patients->get($item['patient_id']); // May fail mid-job
}

// Good - check and refresh token periodically
foreach ($largeDataset as $index => $item) {
    if ($index % 100 === 0) {
        $client->auth()->ensureValidToken();
    }
    $patient = $client->patients->get($item['patient_id']);
}
```

### ❌ PITFALL: Fetching verbose data in loops

```php
// Bad - 50x slower than necessary
foreach ($patientIds as $id) {
    $patient = $client->patients->getWithInsurance($id); // Verbose mode each time
}

// Good - fetch verbose data only when needed
foreach ($patientIds as $id) {
    $patient = $client->patients->get($id);
    if ($patient['has_insurance']) {
        $detailed = $client->patients->getWithInsurance($id);
    }
}
```

### ❌ PITFALL: Not validating webhook signatures

```php
// Bad - accept any webhook
public function webhook(Request $request) {
    $data = $request->json()->all();
    processWebhook($data); // Security risk!
}

// Good - always verify
public function webhook(Request $request) {
    $verifier = new WebhookVerifier($_ENV['SECRET']);
    $event = $verifier->verifyFromRequest($request->getContent(), $request->headers->all());
    processWebhook($event);
}
```

### ❌ PITFALL: Ignoring pagination

```php
// Bad - only processes first page
$patients = $client->patients->list();
foreach ($patients as $patient) {
    // Only processes first 250 patients!
}

// Good - iterate all pages
foreach ($client->patients->iterateAll() as $patient) {
    // Processes ALL patients across all pages
}
```

### ❌ PITFALL: Not handling API errors

```php
// Bad - unhandled exceptions
$appointment = $client->appointments->create($data); // May throw exception

// Good - handle potential errors
try {
    $appointment = $client->appointments->create($data);
} catch (ValidationException $e) {
    return back()->withErrors($e->getValidationErrors());
} catch (ApiException $e) {
    logger()->error('Appointment creation failed', ['error' => $e->getMessage()]);
    return back()->with('error', 'Unable to create appointment. Please try again.');
}
```

---

## Performance Checklist

- [ ] Using iterateAll() for large datasets
- [ ] Applying server-side filters before fetching
- [ ] Using verbose mode only when necessary
- [ ] Caching reference data (doctors, offices, etc.)
- [ ] Implementing token refresh logic
- [ ] Handling rate limits gracefully
- [ ] Queuing long-running operations
- [ ] Logging API errors with context
- [ ] Monitoring API performance metrics
- [ ] Using appropriate pagination strategy for use case

---

## Security Checklist

- [ ] Storing tokens securely (encrypted, env vars)
- [ ] Verifying all webhook signatures
- [ ] Using HTTPS for all API calls
- [ ] Implementing proper error handling (no sensitive data in logs)
- [ ] Limiting OAuth scopes to minimum required
- [ ] Implementing request rate limiting
- [ ] Validating all user input before API calls
- [ ] Implementing CSRF protection on webhook endpoints
- [ ] Regular token rotation
- [ ] Audit logging for sensitive operations

---

## Support

For questions or issues:
- **SDK Issues**: [GitHub Issues](https://github.com/drchrono/DrChrono-PHP/issues)
- **API Questions**: [DrChrono API Docs](https://app.drchrono.com/api-docs/)
- **Email**: api@drchrono.com

---

**Last Updated**: 2025-11-23

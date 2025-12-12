# Laravel Integration Guide

This guide shows you how to integrate the DrChrono PHP SDK into your Laravel application with best practices for production use.

---

## Table of Contents

1. [Installation](#installation)
2. [Configuration](#configuration)
3. [Service Provider Setup](#service-provider-setup)
4. [Creating a Service Class](#creating-a-service-class)
5. [OAuth Flow Implementation](#oauth-flow-implementation)
6. [Controller Examples](#controller-examples)
7. [Middleware for Token Management](#middleware-for-token-management)
8. [Queue Jobs](#queue-jobs)
9. [Testing](#testing)
10. [Production Considerations](#production-considerations)

---

## Installation

Install the DrChrono SDK via Composer:

```bash
composer require drchrono/php-sdk
```

---

## Configuration

### 1. Add Environment Variables

Add DrChrono credentials to your `.env` file:

```env
DRCHRONO_CLIENT_ID=your_client_id
DRCHRONO_CLIENT_SECRET=your_client_secret
DRCHRONO_REDIRECT_URI=https://yourapp.com/auth/drchrono/callback
DRCHRONO_ACCESS_TOKEN=
DRCHRONO_REFRESH_TOKEN=
DRCHRONO_TOKEN_EXPIRES_AT=
```

### 2. Create Configuration File

Create `config/drchrono.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DrChrono API Credentials
    |--------------------------------------------------------------------------
    */
    'client_id' => env('DRCHRONO_CLIENT_ID'),
    'client_secret' => env('DRCHRONO_CLIENT_SECRET'),
    'redirect_uri' => env('DRCHRONO_REDIRECT_URI'),

    /*
    |--------------------------------------------------------------------------
    | OAuth Scopes
    |--------------------------------------------------------------------------
    | Define the scopes your application needs
    */
    'scopes' => [
        'patients:read',
        'patients:write',
        'appointments:read',
        'appointments:write',
        'clinical:read',
        'clinical:write',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    */
    'timeout' => env('DRCHRONO_TIMEOUT', 60),
    'connect_timeout' => env('DRCHRONO_CONNECT_TIMEOUT', 10),
    'max_retries' => env('DRCHRONO_MAX_RETRIES', 3),
    'debug' => env('DRCHRONO_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */
    'cache' => [
        // Cache doctors list for 24 hours
        'doctors_ttl' => 86400,
        // Cache offices list for 12 hours
        'offices_ttl' => 43200,
        // Cache appointment profiles for 1 hour
        'appointment_profiles_ttl' => 3600,
        // Cache patient data for 5 minutes only (HIPAA consideration)
        'patients_ttl' => 300,
    ],
];
```

---

## Service Provider Setup

### 1. Create Service Provider

```bash
php artisan make:provider DrChronoServiceProvider
```

### 2. Implement Provider

`app/Providers/DrChronoServiceProvider.php`:

```php
<?php

namespace App\Providers;

use DrChrono\DrChronoClient;
use DrChrono\Client\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class DrChronoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(DrChronoClient::class, function ($app) {
            // For authenticated users with stored tokens
            if (Auth::check() && Auth::user()->drchrono_access_token) {
                $config = new Config([
                    'access_token' => decrypt(Auth::user()->drchrono_access_token),
                    'refresh_token' => decrypt(Auth::user()->drchrono_refresh_token),
                    'token_expiry' => Auth::user()->drchrono_token_expires_at,
                    'client_id' => config('drchrono.client_id'),
                    'client_secret' => config('drchrono.client_secret'),
                    'timeout' => config('drchrono.timeout'),
                    'debug' => config('drchrono.debug'),
                ]);

                return new DrChronoClient($config);
            }

            // For OAuth flow
            return DrChronoClient::withOAuth(
                clientId: config('drchrono.client_id'),
                clientSecret: config('drchrono.client_secret'),
                redirectUri: config('drchrono.redirect_uri')
            );
        });

        // Bind DrChrono service
        $this->app->singleton(\App\Services\DrChronoService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../../config/drchrono.php' => config_path('drchrono.php'),
        ], 'drchrono-config');
    }
}
```

### 3. Register Provider

Add to `config/app.php`:

```php
'providers' => [
    // ...
    App\Providers\DrChronoServiceProvider::class,
],
```

---

## Creating a Service Class

### DrChronoService Class

`app/Services/DrChronoService.php`:

```php
<?php

namespace App\Services;

use DrChrono\DrChronoClient;
use DrChrono\Exception\{ApiException, RateLimitException, ValidationException};
use Illuminate\Support\Facades\{Auth, Cache, Log};

class DrChronoService
{
    public function __construct(
        private DrChronoClient $client
    ) {}

    /**
     * Ensure token is valid, refresh if necessary
     */
    public function ensureValidToken(): void
    {
        if ($this->client->getConfig()->isTokenExpired()) {
            $this->refreshToken();
        }
    }

    /**
     * Refresh access token
     */
    private function refreshToken(): void
    {
        try {
            $tokens = $this->client->auth()->refreshAccessToken();

            // Update user's tokens
            $user = Auth::user();
            $user->update([
                'drchrono_access_token' => encrypt($tokens['access_token']),
                'drchrono_refresh_token' => encrypt($tokens['refresh_token']),
                'drchrono_token_expires_at' => $tokens['expires_at'],
            ]);

            // Update client config
            $this->client->getConfig()->setAccessToken($tokens['access_token']);

            Log::info('DrChrono token refreshed', ['user_id' => $user->id]);
        } catch (ApiException $e) {
            Log::error('Failed to refresh DrChrono token', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Execute request with automatic token refresh
     */
    public function execute(callable $callback): mixed
    {
        $this->ensureValidToken();

        try {
            return $callback($this->client);
        } catch (RateLimitException $e) {
            Log::warning('DrChrono rate limit hit', [
                'retry_after' => $e->getRetryAfter()
            ]);
            sleep($e->getRetryAfter());
            return $callback($this->client);
        }
    }

    /**
     * Get doctors with caching
     */
    public function getDoctors(): array
    {
        return Cache::remember(
            'drchrono:doctors',
            config('drchrono.cache.doctors_ttl'),
            fn() => $this->execute(fn($client) => $client->doctors->list()->getItems())
        );
    }

    /**
     * Get offices with caching
     */
    public function getOffices(): array
    {
        return Cache::remember(
            'drchrono:offices',
            config('drchrono.cache.offices_ttl'),
            fn() => $this->execute(fn($client) => $client->offices->listAll())
        );
    }

    /**
     * Get patient with optional caching
     */
    public function getPatient(int $patientId, bool $useCache = true): ?array
    {
        if (!$useCache) {
            return $this->execute(fn($client) => $client->patients->get($patientId));
        }

        return Cache::remember(
            "drchrono:patient:{$patientId}",
            config('drchrono.cache.patients_ttl'),
            fn() => $this->execute(fn($client) => $client->patients->get($patientId))
        );
    }

    /**
     * Create appointment with error handling
     */
    public function createAppointment(array $data): array
    {
        try {
            return $this->execute(function($client) use ($data) {
                return $client->appointments->createAppointment($data);
            });
        } catch (ValidationException $e) {
            Log::warning('Appointment validation failed', [
                'errors' => $e->getValidationErrors(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    /**
     * Clear cache for specific resources
     */
    public function clearCache(string $resource, ?int $id = null): void
    {
        if ($id) {
            Cache::forget("drchrono:{$resource}:{$id}");
        } else {
            Cache::forget("drchrono:{$resource}");
        }
    }
}
```

---

## OAuth Flow Implementation

### 1. Add Routes

`routes/web.php`:

```php
<?php

use App\Http\Controllers\DrChronoAuthController;

Route::middleware(['web'])->group(function () {
    Route::get('/auth/drchrono', [DrChronoAuthController::class, 'redirect'])
        ->name('drchrono.auth');

    Route::get('/auth/drchrono/callback', [DrChronoAuthController::class, 'callback'])
        ->name('drchrono.callback');

    Route::post('/auth/drchrono/disconnect', [DrChronoAuthController::class, 'disconnect'])
        ->middleware('auth')
        ->name('drchrono.disconnect');
});
```

### 2. Create Controller

`app/Http/Controllers/DrChronoAuthController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Services\DrChronoService;
use DrChrono\DrChronoClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DrChronoAuthController extends Controller
{
    public function __construct(
        private DrChronoClient $client
    ) {}

    /**
     * Redirect to DrChrono for authorization
     */
    public function redirect()
    {
        $authUrl = $this->client->auth()->getAuthorizationUrl(
            config('drchrono.scopes')
        );

        // Store state in session for CSRF protection
        session(['drchrono_oauth_state' => $this->client->auth()->getState()]);

        return redirect($authUrl);
    }

    /**
     * Handle OAuth callback
     */
    public function callback(Request $request)
    {
        // Verify state (CSRF protection)
        if ($request->get('state') !== session('drchrono_oauth_state')) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid OAuth state. Please try again.');
        }

        try {
            // Exchange authorization code for tokens
            $tokens = $this->client->auth()->exchangeAuthorizationCode(
                $request->get('code')
            );

            // Store tokens for current user
            $user = Auth::user();
            $user->update([
                'drchrono_access_token' => encrypt($tokens['access_token']),
                'drchrono_refresh_token' => encrypt($tokens['refresh_token']),
                'drchrono_token_expires_at' => $tokens['expires_at'],
            ]);

            Log::info('DrChrono connected', ['user_id' => $user->id]);

            return redirect()->route('dashboard')
                ->with('success', 'DrChrono connected successfully!');

        } catch (\Exception $e) {
            Log::error('DrChrono OAuth failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Failed to connect to DrChrono. Please try again.');
        }
    }

    /**
     * Disconnect DrChrono
     */
    public function disconnect()
    {
        $user = Auth::user();
        $user->update([
            'drchrono_access_token' => null,
            'drchrono_refresh_token' => null,
            'drchrono_token_expires_at' => null,
        ]);

        Log::info('DrChrono disconnected', ['user_id' => $user->id]);

        return redirect()->route('dashboard')
            ->with('success', 'DrChrono disconnected successfully');
    }
}
```

### 3. Add Database Columns

Create migration:

```bash
php artisan make:migration add_drchrono_tokens_to_users_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('drchrono_access_token')->nullable();
            $table->text('drchrono_refresh_token')->nullable();
            $table->timestamp('drchrono_token_expires_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'drchrono_access_token',
                'drchrono_refresh_token',
                'drchrono_token_expires_at'
            ]);
        });
    }
};
```

---

## Controller Examples

### Patient Controller

`app/Http/Controllers/PatientController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Services\DrChronoService;
use Illuminate\Http\Request;
use DrChrono\Exception\{ValidationException, ApiException};

class PatientController extends Controller
{
    public function __construct(
        private DrChronoService $drchrono
    ) {}

    /**
     * List patients
     */
    public function index(Request $request)
    {
        try {
            $patients = $this->drchrono->execute(function($client) use ($request) {
                return $client->patients->list([
                    'doctor' => $request->get('doctor_id'),
                    'page_size' => 50,
                ]);
            });

            return view('patients.index', [
                'patients' => $patients->getItems(),
                'pagination' => [
                    'has_next' => $patients->hasNext(),
                    'has_previous' => $patients->hasPrevious(),
                ]
            ]);
        } catch (ApiException $e) {
            return back()->with('error', 'Failed to load patients');
        }
    }

    /**
     * Show patient
     */
    public function show(int $id)
    {
        try {
            $patient = $this->drchrono->getPatient($id);

            if (!$patient) {
                abort(404);
            }

            return view('patients.show', compact('patient'));
        } catch (ApiException $e) {
            return back()->with('error', 'Failed to load patient');
        }
    }

    /**
     * Create patient
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'required|email',
            'doctor' => 'required|integer',
        ]);

        try {
            $patient = $this->drchrono->execute(function($client) use ($validated) {
                return $client->patients->createPatient($validated);
            });

            return redirect()->route('patients.show', $patient['id'])
                ->with('success', 'Patient created successfully');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->getValidationErrors())
                ->withInput();
        } catch (ApiException $e) {
            return back()
                ->with('error', 'Failed to create patient')
                ->withInput();
        }
    }
}
```

---

## Middleware for Token Management

### Create Middleware

```bash
php artisan make:middleware EnsureDrChronoToken
```

`app/Http/Middleware/EnsureDrChronoToken.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDrChronoToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()?->drchrono_access_token) {
            return redirect()->route('drchrono.auth')
                ->with('warning', 'Please connect your DrChrono account first');
        }

        return $next($request);
    }
}
```

### Register Middleware

`app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ...
    'drchrono' => \App\Http\Middleware\EnsureDrChronoToken::class,
];
```

### Use Middleware

```php
Route::middleware(['auth', 'drchrono'])->group(function () {
    Route::resource('patients', PatientController::class);
    Route::resource('appointments', AppointmentController::class);
});
```

---

## Queue Jobs

### Sync Appointments Job

```bash
php artisan make:job SyncDrChronoAppointments
```

`app/Jobs/SyncDrChronoAppointments.php`:

```php
<?php

namespace App\Jobs;

use App\Services\DrChronoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\{InteractsWithQueue, SerializesModels};
use Illuminate\Support\Facades\Log;

class SyncDrChronoAppointments implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 300; // 5 minutes

    public function __construct(
        private int $doctorId,
        private string $startDate,
        private string $endDate
    ) {}

    public function handle(DrChronoService $drchrono): void
    {
        try {
            $appointments = $drchrono->execute(function($client) {
                return $client->appointments->listByDateRange(
                    startDate: $this->startDate,
                    endDate: $this->endDate,
                    filters: ['doctor' => $this->doctorId]
                );
            });

            foreach ($appointments->iterateAll() as $appointment) {
                // Sync to local database
                \App\Models\Appointment::updateOrCreate(
                    ['drchrono_id' => $appointment['id']],
                    [
                        'patient_id' => $appointment['patient'],
                        'doctor_id' => $appointment['doctor'],
                        'scheduled_time' => $appointment['scheduled_time'],
                        'status' => $appointment['status'],
                        // ... other fields
                    ]
                );
            }

            Log::info('DrChrono appointments synced', [
                'doctor_id' => $this->doctorId,
                'date_range' => "{$this->startDate} - {$this->endDate}"
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to sync DrChrono appointments', [
                'doctor_id' => $this->doctorId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
```

### Dispatch Job

```php
use App\Jobs\SyncDrChronoAppointments;

// Dispatch immediately
SyncDrChronoAppointments::dispatch($doctorId, $startDate, $endDate);

// Dispatch with delay
SyncDrChronoAppointments::dispatch($doctorId, $startDate, $endDate)
    ->delay(now()->addMinutes(5));

// Schedule daily sync
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->job(new SyncDrChronoAppointments(
        doctorId: config('drchrono.default_doctor_id'),
        startDate: now()->toDateString(),
        endDate: now()->addDays(7)->toDateString()
    ))->daily();
}
```

---

## Testing

### Feature Test

`tests/Feature/DrChronoIntegrationTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Services\DrChronoService;
use DrChrono\DrChronoClient;
use DrChrono\Client\HttpClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DrChronoIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_patient()
    {
        // Mock HTTP client
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->method('get')
            ->willReturn([
                'id' => 123,
                'first_name' => 'John',
                'last_name' => 'Doe'
            ]);

        // Create client with mocked HTTP
        $client = new DrChronoClient(['access_token' => 'test']);
        $client->setHttpClient($httpClient);

        // Bind in container
        $this->app->instance(DrChronoClient::class, $client);

        // Test service
        $service = $this->app->make(DrChronoService::class);
        $patient = $service->execute(fn($c) => $c->patients->get(123));

        $this->assertEquals('John', $patient['first_name']);
    }
}
```

---

## Production Considerations

### 1. Token Security

- Always encrypt tokens in database
- Use Laravel's built-in encryption
- Never log unencrypted tokens
- Implement token rotation

### 2. Rate Limiting

```php
// Add rate limiting to routes
Route::middleware(['auth', 'drchrono', 'throttle:60,1'])->group(function () {
    // Routes that call DrChrono API
});
```

### 3. Error Monitoring

```php
// config/logging.php
'channels' => [
    'drchrono' => [
        'driver' => 'daily',
        'path' => storage_path('logs/drchrono.log'),
        'level' => 'info',
        'days' => 14,
    ],
],
```

### 4. Cache Warming

```php
// Warm cache during deployment
php artisan tinker
>>> app(DrChronoService::class)->getDoctors();
>>> app(DrChronoService::class)->getOffices();
```

### 5. Health Checks

```php
// routes/api.php
Route::get('/health/drchrono', function (DrChronoService $drchrono) {
    try {
        $drchrono->execute(fn($client) => $client->users->getCurrent());
        return response()->json(['status' => 'healthy']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'unhealthy'], 500);
    }
});
```

---

## Complete Example Application

See the `/examples/laravel_integration/` directory for a complete Laravel application demonstrating all integration patterns.

---

## Support

- **SDK Issues**: [GitHub Issues](https://github.com/drchrono/DrChrono-PHP/issues)
- **Laravel Questions**: Laravel documentation + this guide
- **API Questions**: [DrChrono API Docs](https://app.drchrono.com/api-docs/)

---

**Last Updated**: 2025-11-23

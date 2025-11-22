<?php

declare(strict_types=1);

namespace DrChrono;

use DrChrono\Auth\OAuth2Handler;
use DrChrono\Client\Config;
use DrChrono\Client\HttpClient;
use DrChrono\Resource\AppointmentsResource;
use DrChrono\Resource\ClinicalNotesResource;
use DrChrono\Resource\DocumentsResource;
use DrChrono\Resource\OfficesResource;
use DrChrono\Resource\PatientsResource;
use DrChrono\Resource\UsersResource;
use DrChrono\Resource\TasksResource;
use DrChrono\Resource\PrescriptionsResource;
use DrChrono\Resource\LabOrdersResource;
use DrChrono\Resource\LabTestsResource;
use DrChrono\Resource\LabResultsResource;
use DrChrono\Resource\LabDocumentsResource;
use DrChrono\Resource\InsurancesResource;
use DrChrono\Resource\AllergiesResource;
use DrChrono\Resource\MedicationsResource;
use DrChrono\Resource\ProblemsResource;
use DrChrono\Resource\VitalsResource;
use DrChrono\Resource\ImmunizationsResource;
use DrChrono\Resource\BillingResource;
use DrChrono\Resource\ClaimBillingNotesResource;
use DrChrono\Resource\CustomVitalsResource;
use DrChrono\Resource\MessagesResource;
use DrChrono\Resource\ReminderProfilesResource;
use DrChrono\Resource\SublabsResource;
use DrChrono\Resource\InventoryVaccinesResource;

/**
 * Main DrChrono SDK client
 *
 * @property-read PatientsResource $patients
 * @property-read AppointmentsResource $appointments
 * @property-read ClinicalNotesResource $clinicalNotes
 * @property-read DocumentsResource $documents
 * @property-read OfficesResource $offices
 * @property-read UsersResource $users
 * @property-read TasksResource $tasks
 * @property-read PrescriptionsResource $prescriptions
 * @property-read LabOrdersResource $labOrders
 * @property-read LabTestsResource $labTests
 * @property-read LabResultsResource $labResults
 * @property-read LabDocumentsResource $labDocuments
 * @property-read InsurancesResource $insurances
 * @property-read AllergiesResource $allergies
 * @property-read MedicationsResource $medications
 * @property-read ProblemsResource $problems
 * @property-read VitalsResource $vitals
 * @property-read ImmunizationsResource $immunizations
 * @property-read BillingResource $billing
 * @property-read MessagesResource $messages
 */
class DrChronoClient
{
    private Config $config;
    private HttpClient $httpClient;
    private OAuth2Handler $oauth;
    private array $resources = [];

    public function __construct(array|Config $config)
    {
        $this->config = $config instanceof Config ? $config : new Config($config);
        $this->httpClient = new HttpClient($this->config);
        $this->oauth = new OAuth2Handler($this->config, $this->httpClient);
    }

    /**
     * Create client with access token
     */
    public static function withAccessToken(string $accessToken, array $options = []): self
    {
        $options['access_token'] = $accessToken;
        return new self($options);
    }

    /**
     * Create client with OAuth credentials
     */
    public static function withOAuth(
        string $clientId,
        string $clientSecret,
        string $redirectUri,
        array $options = []
    ): self {
        $options['client_id'] = $clientId;
        $options['client_secret'] = $clientSecret;
        $options['redirect_uri'] = $redirectUri;
        return new self($options);
    }

    /**
     * Get OAuth2 handler for authentication flow
     */
    public function auth(): OAuth2Handler
    {
        return $this->oauth;
    }

    /**
     * Get configuration
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * Get HTTP client
     */
    public function getHttpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * Magic method to access resources
     */
    public function __get(string $name)
    {
        return $this->getResource($name);
    }

    /**
     * Get or create resource instance
     */
    private function getResource(string $name)
    {
        if (isset($this->resources[$name])) {
            return $this->resources[$name];
        }

        $resource = match ($name) {
            'patients' => new PatientsResource($this->httpClient),
            'appointments' => new AppointmentsResource($this->httpClient),
            'clinicalNotes' => new ClinicalNotesResource($this->httpClient),
            'documents' => new DocumentsResource($this->httpClient),
            'offices' => new OfficesResource($this->httpClient),
            'users' => new UsersResource($this->httpClient),
            'tasks' => new TasksResource($this->httpClient),
            'prescriptions' => new PrescriptionsResource($this->httpClient),
            'labOrders' => new LabOrdersResource($this->httpClient),
            'labTests' => new LabTestsResource($this->httpClient),
            'labResults' => new LabResultsResource($this->httpClient),
            'labDocuments' => new LabDocumentsResource($this->httpClient),
            'insurances' => new InsurancesResource($this->httpClient),
            'allergies' => new AllergiesResource($this->httpClient),
            'medications' => new MedicationsResource($this->httpClient),
            'problems' => new ProblemsResource($this->httpClient),
            'vitals' => new VitalsResource($this->httpClient),
            'immunizations' => new ImmunizationsResource($this->httpClient),
            'billing' => new BillingResource($this->httpClient),
            'claimBillingNotes' => new ClaimBillingNotesResource($this->httpClient),
            'customVitals' => new CustomVitalsResource($this->httpClient),
            'messages' => new MessagesResource($this->httpClient),
            'reminderProfiles' => new ReminderProfilesResource($this->httpClient),
            'sublabs' => new SublabsResource($this->httpClient),
            'inventoryVaccines' => new InventoryVaccinesResource($this->httpClient),
            default => throw new \InvalidArgumentException("Unknown resource: {$name}"),
        };

        $this->resources[$name] = $resource;
        return $resource;
    }

    /**
     * Get current authenticated user
     */
    public function getCurrentUser(): array
    {
        return $this->httpClient->get('/api/users/current');
    }
}

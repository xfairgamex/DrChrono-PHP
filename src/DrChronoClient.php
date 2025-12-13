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
use DrChrono\Resource\AppointmentProfilesResource;
use DrChrono\Resource\AppointmentTemplatesResource;
use DrChrono\Resource\CustomAppointmentFieldsResource;
use DrChrono\Resource\PatientPaymentsResource;
use DrChrono\Resource\PatientMessagesResource;
use DrChrono\Resource\PatientsSummaryResource;
use DrChrono\Resource\CustomDemographicsResource;
use DrChrono\Resource\PatientFlagTypesResource;
use DrChrono\Resource\BillingProfilesResource;
use DrChrono\Resource\EligibilityChecksResource;
use DrChrono\Resource\FeeSchedulesResource;
use DrChrono\Resource\TransactionsResource;
use DrChrono\Resource\LineItemsResource;
use DrChrono\Resource\PatientPaymentLogResource;
use DrChrono\Resource\ConsentFormsResource;
use DrChrono\Resource\CustomInsurancePlanNamesResource;
use DrChrono\Resource\ClinicalNoteTemplatesResource;
use DrChrono\Resource\ClinicalNoteFieldTypesResource;
use DrChrono\Resource\ClinicalNoteFieldValuesResource;
use DrChrono\Resource\ProceduresResource;
use DrChrono\Resource\AmendmentsResource;
use DrChrono\Resource\CarePlansResource;
use DrChrono\Resource\PatientRiskAssessmentsResource;
use DrChrono\Resource\PatientPhysicalExamsResource;
use DrChrono\Resource\PatientInterventionsResource;
use DrChrono\Resource\PatientCommunicationsResource;
use DrChrono\Resource\ImplantableDevicesResource;
use DrChrono\Resource\InventoryCategoriesResource;
use DrChrono\Resource\PatientVaccineRecordsResource;
use DrChrono\Resource\TaskTemplatesResource;
use DrChrono\Resource\TaskCategoriesResource;
use DrChrono\Resource\TaskStatusesResource;
use DrChrono\Resource\TaskNotesResource;
use DrChrono\Resource\DoctorsResource;
use DrChrono\Resource\UserGroupsResource;
use DrChrono\Resource\PrescriptionMessagesResource;
use DrChrono\Resource\CommLogsResource;

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
 * @property-read AppointmentProfilesResource $appointmentProfiles
 * @property-read AppointmentTemplatesResource $appointmentTemplates
 * @property-read CustomAppointmentFieldsResource $customAppointmentFields
 * @property-read PatientPaymentsResource $patientPayments
 * @property-read PatientMessagesResource $patientMessages
 * @property-read PatientsSummaryResource $patientsSummary
 * @property-read CustomDemographicsResource $customDemographics
 * @property-read PatientFlagTypesResource $patientFlagTypes
 * @property-read BillingProfilesResource $billingProfiles
 * @property-read EligibilityChecksResource $eligibilityChecks
 * @property-read FeeSchedulesResource $feeSchedules
 * @property-read TransactionsResource $transactions
 * @property-read LineItemsResource $lineItems
 * @property-read PatientPaymentLogResource $patientPaymentLog
 * @property-read ConsentFormsResource $consentForms
 * @property-read CustomInsurancePlanNamesResource $customInsurancePlanNames
 * @property-read ClinicalNoteTemplatesResource $clinicalNoteTemplates
 * @property-read ClinicalNoteFieldTypesResource $clinicalNoteFieldTypes
 * @property-read ClinicalNoteFieldValuesResource $clinicalNoteFieldValues
 * @property-read ProceduresResource $procedures
 * @property-read AmendmentsResource $amendments
 * @property-read CarePlansResource $carePlans
 * @property-read PatientRiskAssessmentsResource $patientRiskAssessments
 * @property-read PatientPhysicalExamsResource $patientPhysicalExams
 * @property-read PatientInterventionsResource $patientInterventions
 * @property-read PatientCommunicationsResource $patientCommunications
 * @property-read ImplantableDevicesResource $implantableDevices
 * @property-read InventoryCategoriesResource $inventoryCategories
 * @property-read PatientVaccineRecordsResource $patientVaccineRecords
 * @property-read TaskTemplatesResource $taskTemplates
 * @property-read TaskCategoriesResource $taskCategories
 * @property-read TaskStatusesResource $taskStatuses
 * @property-read TaskNotesResource $taskNotes
 * @property-read DoctorsResource $doctors
 * @property-read UserGroupsResource $userGroups
 * @property-read PrescriptionMessagesResource $prescriptionMessages
 * @property-read CommLogsResource $commLogs
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
            'appointmentProfiles' => new AppointmentProfilesResource($this->httpClient),
            'appointmentTemplates' => new AppointmentTemplatesResource($this->httpClient),
            'customAppointmentFields' => new CustomAppointmentFieldsResource($this->httpClient),
            'patientPayments' => new PatientPaymentsResource($this->httpClient),
            'patientMessages' => new PatientMessagesResource($this->httpClient),
            'patientsSummary' => new PatientsSummaryResource($this->httpClient),
            'customDemographics' => new CustomDemographicsResource($this->httpClient),
            'patientFlagTypes' => new PatientFlagTypesResource($this->httpClient),
            'billingProfiles' => new BillingProfilesResource($this->httpClient),
            'eligibilityChecks' => new EligibilityChecksResource($this->httpClient),
            'feeSchedules' => new FeeSchedulesResource($this->httpClient),
            'transactions' => new TransactionsResource($this->httpClient),
            'lineItems' => new LineItemsResource($this->httpClient),
            'patientPaymentLog' => new PatientPaymentLogResource($this->httpClient),
            'consentForms' => new ConsentFormsResource($this->httpClient),
            'customInsurancePlanNames' => new CustomInsurancePlanNamesResource($this->httpClient),
            'clinicalNoteTemplates' => new ClinicalNoteTemplatesResource($this->httpClient),
            'clinicalNoteFieldTypes' => new ClinicalNoteFieldTypesResource($this->httpClient),
            'clinicalNoteFieldValues' => new ClinicalNoteFieldValuesResource($this->httpClient),
            'procedures' => new ProceduresResource($this->httpClient),
            'amendments' => new AmendmentsResource($this->httpClient),
            'carePlans' => new CarePlansResource($this->httpClient),
            'patientRiskAssessments' => new PatientRiskAssessmentsResource($this->httpClient),
            'patientPhysicalExams' => new PatientPhysicalExamsResource($this->httpClient),
            'patientInterventions' => new PatientInterventionsResource($this->httpClient),
            'patientCommunications' => new PatientCommunicationsResource($this->httpClient),
            'implantableDevices' => new ImplantableDevicesResource($this->httpClient),
            'inventoryCategories' => new InventoryCategoriesResource($this->httpClient),
            'patientVaccineRecords' => new PatientVaccineRecordsResource($this->httpClient),
            'taskTemplates' => new TaskTemplatesResource($this->httpClient),
            'taskCategories' => new TaskCategoriesResource($this->httpClient),
            'taskStatuses' => new TaskStatusesResource($this->httpClient),
            'taskNotes' => new TaskNotesResource($this->httpClient),
            'doctors' => new DoctorsResource($this->httpClient),
            'userGroups' => new UserGroupsResource($this->httpClient),
            'prescriptionMessages' => new PrescriptionMessagesResource($this->httpClient),
            'commLogs' => new CommLogsResource($this->httpClient),
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

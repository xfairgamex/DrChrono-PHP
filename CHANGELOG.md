# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-01-15

### Added
- Initial release of DrChrono PHP SDK
- Complete OAuth2 authentication flow with token refresh
- Full coverage of DrChrono API endpoints:
  - Patients management
  - Appointments scheduling
  - Clinical notes and documentation
  - Document upload and management
  - Offices and users
  - Tasks and prescriptions
  - Laboratory orders, tests, and results
  - Insurance information
  - Allergies, medications, and problems
  - Vital signs and immunizations
  - Billing and transactions
- Automatic pagination with memory-efficient generators
- Type-safe model classes (Patient, Appointment, Office, User)
- Webhook verification and event handling
- Intelligent retry logic for rate limits
- Comprehensive error handling with specific exception classes
- PSR-4 autoloading
- PSR-12 coding standards
- Complete test suite with PHPUnit
- Extensive documentation and examples
- Framework-agnostic design

### Features
- PHP 8.1+ support with modern type hints
- Guzzle HTTP client integration
- Configurable timeouts and retry strategies
- Debug mode for development
- HMAC signature verification for webhooks
- JWT token verification for iframe integration
- Automatic token refresh on expiration

### Documentation
- Comprehensive README with quick start guide
- 6 runnable example scripts
- Complete API reference
- Contributing guidelines
- MIT License

## [Unreleased]

## [1.3.0] - 2025-11-23

### Added - Phase 2 Billing & Financial Resources (COMPLETED)

#### New Resources (6)
- Added **FeeSchedulesResource** - Pricing and fee schedule management
  - `list()` - List fee schedules with filters
  - `listByDoctor()` - Get schedules for specific doctor
  - `get()` - Get specific fee schedule
  - `createSchedule()` - Create new fee schedule
  - `updateSchedule()` - Update fee schedule
  - `deleteSchedule()` - Delete fee schedule
  - `getByCode()` - Get fees by procedure code

- Added **TransactionsResource** - Payment transaction management
  - `list()` - List transactions with comprehensive filters
  - `listByPatient()` - Get transactions for specific patient
  - `listByAppointment()` - Get transactions for specific appointment
  - `listByDoctor()` - Get transactions for specific doctor
  - `get()` - Get specific transaction
  - `createTransaction()` - Create new transaction
  - `updateTransaction()` - Update transaction
  - `deleteTransaction()` - Delete transaction
  - `recordPayment()` - Convenience method for recording payments
  - `recordAdjustment()` - Convenience method for recording adjustments

- Added **LineItemsResource** - Invoice line item management
  - `list()` - List line items with filters
  - `listByAppointment()` - Get line items for specific appointment
  - `listByDoctor()` - Get line items for specific doctor
  - `listByPatient()` - Get line items for specific patient
  - `get()` - Get specific line item
  - `createLineItem()` - Create new line item
  - `updateLineItem()` - Update line item
  - `deleteLineItem()` - Delete line item
  - `listByCode()` - Get line items by procedure code
  - `addProcedure()` - Convenience method to add procedure to appointment

- Added **PatientPaymentLogResource** - Payment history and audit trail
  - `list()` - List payment log entries
  - `listByPatient()` - Get log entries for specific patient
  - `listByPayment()` - Get log entries for specific payment
  - `listByDoctor()` - Get log entries for specific doctor
  - `get()` - Get specific log entry
  - `getPaymentHistory()` - Get payment history for patient
  - `getRecentActivity()` - Get recent payment activity

- Added **ConsentFormsResource** - Patient consent form management
  - `list()` - List consent forms
  - `listByPatient()` - Get consent forms for specific patient
  - `listByDoctor()` - Get consent forms for specific doctor
  - `get()` - Get specific consent form
  - `createForm()` - Create new consent form
  - `updateForm()` - Update consent form
  - `deleteForm()` - Delete consent form
  - `markAsSigned()` - Mark form as signed
  - `getUnsignedForms()` - Get unsigned forms for patient

- Added **CustomInsurancePlanNamesResource** - Custom insurance plan naming
  - `list()` - List custom plan names
  - `listByDoctor()` - Get custom names for specific doctor
  - `get()` - Get specific custom plan name
  - `createPlanName()` - Create new custom plan name
  - `updatePlanName()` - Update custom plan name
  - `deletePlanName()` - Delete custom plan name
  - `setCustomName()` - Convenience method to set custom name

#### New Models (5)
- Added **FeeSchedule** model with pricing properties
- Added **Transaction** model with payment tracking (`isPayment()`, `isAdjustment()` helpers)
- Added **LineItem** model with billing code properties and `getTotal()` calculator
- Added **ConsentForm** model with consent tracking (`isSigned()`, `requiresSignature()` helpers)
- Added **CustomInsurancePlanName** model for custom plan naming

#### Unit Tests (11)
- Added FeeSchedulesResourceTest (7 tests)
- Added TransactionsResourceTest (10 tests)
- Added LineItemsResourceTest (10 tests)
- Added PatientPaymentLogResourceTest (7 tests)
- Added ConsentFormsResourceTest (9 tests)
- Added CustomInsurancePlanNamesResourceTest (7 tests)
- Added FeeScheduleTest (5 tests)
- Added TransactionTest (6 tests)
- Added LineItemTest (6 tests)
- Added ConsentFormTest (6 tests)
- Added CustomInsurancePlanNameTest (4 tests)
- Total: 77 new tests, 164+ tests passing overall

### Improved
- Updated DrChronoClient with 6 new Phase 2 resources
- Updated README API reference with all new resources
- Enhanced documentation with detailed method descriptions
- Improved PHPDoc coverage across all new files

### Technical
- Phase 2 complete: 8/8 billing resources implemented (100%)
- API coverage increased to 60%+ (43/69 endpoints)
- All resources follow established patterns from IMPLEMENTATION_GUIDE.md
- Code follows PSR-12 standards
- Comprehensive unit test coverage for all new functionality

## [1.2.0] - 2025-11-23

### Added - Phase 1 Testing & Phase 2 Start

#### Unit Testing Suite (Complete Phase 1)
- Added comprehensive unit tests for all Phase 1 resources (8 test files)
  - AppointmentProfilesResourceTest
  - AppointmentTemplatesResourceTest
  - CustomAppointmentFieldsResourceTest
  - PatientPaymentsResourceTest
  - PatientMessagesResourceTest
  - PatientsSummaryResourceTest
  - CustomDemographicsResourceTest
  - PatientFlagTypesResourceTest
- Added comprehensive unit tests for all Phase 1 models (5 test files)
  - AppointmentProfileTest
  - AppointmentTemplateTest
  - PatientPaymentTest
  - PatientMessageTest
  - PatientFlagTypeTest
- All 108 tests passing with 357 assertions
- Full test coverage for Phase 1 implementation

#### Phase 2: Billing & Financial Resources (Started)
- Added **BillingProfilesResource** - Billing configuration management
  - `list()` - List billing profiles with filters
  - `getByDoctor()` - Get billing profile for specific doctor
  - `createProfile()` - Create new billing profile
  - `updateProfile()` - Update billing profile
- Added **EligibilityChecksResource** - Insurance eligibility verification
  - `list()` - List eligibility checks with filters
  - `listByPatient()` - Get checks for specific patient
  - `listByAppointment()` - Get checks for specific appointment
  - `verify()` - Create and run eligibility check
  - `verifyPrimaryInsurance()` - Check primary insurance
  - `verifySecondaryInsurance()` - Check secondary insurance
- Added **BillingProfile** model with getters/setters
- Added **EligibilityCheck** model with helper methods (`isEligible()`, `hasError()`, `isCompleted()`)

### Improved
- Updated DrChronoClient with 2 new resources (billingProfiles, eligibilityChecks)
- Updated README with new resources in API reference
- Documentation improvements

### Technical
- Test infrastructure established with PHPUnit
- Code style compliance verified (PSR-12)
- PHPStan compatibility maintained
- API Coverage: 51% → 54% (37/69 endpoints)

## [1.1.0] - 2025-11-23

### Added - Phase 1: Foundation & Core Missing Resources

#### Verbose Mode Support (Phase 1.1)
- Added `listVerbose()` and `getVerbose()` methods to AbstractResource
- Added `withVerbose()` helper method for enabling verbose mode
- Added `getWithInsurance()` and `listWithInsurance()` to PatientsResource for retrieving full insurance details
- Added `getWithClinicalData()` and `listWithClinicalData()` to AppointmentsResource for clinical information
- Created comprehensive verbose mode example (`examples/07_verbose_mode.php`)
- Documentation for verbose mode behavior and performance considerations

#### Appointment Extensions (Phase 1.2)
- **AppointmentProfilesResource** - Manage appointment types with standard durations
  - List profiles by doctor and office
  - Create, update, and archive appointment profiles
  - AppointmentProfile model with full type safety
- **AppointmentTemplatesResource** - Manage recurring appointment blocks
  - Define recurring availability patterns
  - Configure by day of week and time
  - AppointmentTemplate model
- **CustomAppointmentFieldsResource** - Custom appointment metadata
  - Create custom fields for capturing additional appointment data
  - Support for multiple field types (text, dropdown, checkbox)

#### Patient Extensions (Phase 1.3)
- **PatientPaymentsResource** - Patient payment records
  - Track copays, deductibles, and out-of-pocket payments
  - List by patient and appointment
  - PatientPayment model with amount and payment method tracking
- **PatientMessagesResource** - Patient communications
  - Send and receive secure messages with patients
  - Mark messages as read/unread
  - Filter by patient and read status
  - PatientMessage model
- **PatientsSummaryResource** - Aggregated patient data
  - Bulk patient summaries
  - Detailed summaries with verbose mode
  - List by doctor with filtering
- **CustomDemographicsResource** - Custom patient demographic fields
  - Define custom demographic fields beyond standard fields
  - Support for various field types
- **PatientFlagTypesResource** - Custom patient flag definitions
  - Define flag types for patient categorization (VIP, high-risk, etc.)
  - Color coding and priority levels
  - PatientFlagType model

#### Models (Phase 1.4)
- **AppointmentProfile** - Appointment profile model with duration, color, and settings
- **AppointmentTemplate** - Recurring appointment template model
- **PatientPayment** - Payment record model with amount and payment method
- **PatientMessage** - Patient message model with read status
- **PatientFlagType** - Flag type model with color and priority

### Changed
- Updated DrChronoClient to register 8 new resources
- Enhanced AbstractResource with verbose mode capabilities
- Updated API coverage from ~39% (27/69 endpoints) to ~51% (35/69 endpoints)

### Documentation
- Added verbose mode usage guide with performance best practices
- Created example demonstrating verbose mode for patients and appointments
- Updated README with new resources (pending)
- Documented verbose mode fields for Patients, Appointments, and Clinical Notes

### Technical Improvements
- All new resources follow consistent naming and method patterns
- PHPDoc comments for all new classes and methods
- Type-safe models with getters and setters
- Fluent interfaces for model building

## [1.0.0] - 2025-01-15

### Planned (Future Releases)
- Batch operations support
- Async request capabilities
- Response caching layer
- Laravel service provider
- Symfony bundle

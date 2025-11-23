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

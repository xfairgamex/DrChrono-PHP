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

### Planned
- Batch operations support
- Async request capabilities
- Response caching layer
- Laravel service provider
- Symfony bundle

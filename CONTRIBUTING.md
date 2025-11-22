# Contributing to DrChrono PHP SDK

First off, thank you for considering contributing to the DrChrono PHP SDK! It's people like you that make this SDK better for everyone.

## Code of Conduct

This project and everyone participating in it is governed by respect and professionalism. Please be kind and courteous to others.

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the existing issues to avoid duplicates. When you create a bug report, include as many details as possible:

- **Use a clear and descriptive title**
- **Describe the exact steps to reproduce the problem**
- **Provide specific examples** (code snippets, API responses, etc.)
- **Describe the behavior you observed** and what you expected
- **Include PHP version**, SDK version, and OS

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion:

- **Use a clear and descriptive title**
- **Provide a detailed description** of the proposed functionality
- **Explain why this enhancement would be useful**
- **Include code examples** if applicable

### Pull Requests

1. **Fork the repository** and create your branch from `main`
2. **Follow PSR-12 coding standards**
3. **Add tests** for any new functionality
4. **Ensure all tests pass** (`composer test`)
5. **Update documentation** as needed
6. **Write clear commit messages**

## Development Setup

```bash
# Clone your fork
git clone https://github.com/yourusername/DrChrono-PHP.git
cd DrChrono-PHP

# Install dependencies
composer install

# Run tests
composer test

# Check code style
composer cs:check

# Fix code style
composer cs:fix

# Run static analysis
composer phpstan
```

## Coding Standards

- Follow **PSR-12** coding standards
- Use **type hints** for all parameters and return values
- Write **PHPDoc comments** for all public methods
- Keep methods focused and concise
- Use **meaningful variable and method names**

## Testing Guidelines

- Write tests for all new features
- Maintain or improve code coverage
- Use descriptive test method names
- Follow the Arrange-Act-Assert pattern
- Mock external dependencies

Example:

```php
public function testPatientCreation(): void
{
    // Arrange
    $data = ['first_name' => 'John', 'last_name' => 'Doe'];

    // Act
    $patient = Patient::fromArray($data);

    // Assert
    $this->assertEquals('John', $patient->getFirstName());
    $this->assertEquals('Doe', $patient->getLastName());
}
```

## Commit Messages

- Use the present tense ("Add feature" not "Added feature")
- Use the imperative mood ("Move cursor to..." not "Moves cursor to...")
- Limit the first line to 72 characters or less
- Reference issues and pull requests after the first line

Examples:
```
Add support for bulk patient import

Implement batch processing for creating multiple patients
in a single API call. Includes tests and documentation.

Fixes #123
```

## Adding New Resources

When adding support for a new DrChrono API endpoint:

1. Create resource class in `src/Resource/`
2. Extend `AbstractResource`
3. Add resource getter in `DrChronoClient`
4. Add corresponding tests
5. Update README documentation
6. Add example usage

## Documentation

- Update `README.md` for user-facing changes
- Update `CHANGELOG.md` following Keep a Changelog format
- Add PHPDoc comments for all public APIs
- Include code examples for new features
- Update examples/ directory if applicable

## Questions?

Feel free to open an issue with the label "question" or email us at api@drchrono.com

## License

By contributing, you agree that your contributions will be licensed under the MIT License.

Thank you for contributing! 🎉

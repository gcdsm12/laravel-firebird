# Firebird for Laravel 12 and 13

Firebird PDO database driver maintained in [gcdsm12/laravel-firebird](https://github.com/gcdsm12/laravel-firebird).
This fork preserves legacy Firebird 2.5 query syntax, including FIRST/SKIP pagination and dialects 1 and 3.

## Origin and credits

This repository is a fork of [onezerotrash/laravel-firebird](https://github.com/onezerotrash/laravel-firebird), based on upstream commit fe0f21717a74886ffa1c6aef0dac204c8183c131 (v0.0.2).
That project is itself based on [harrygulliford/laravel-firebird](https://github.com/harrygulliford/laravel-firebird).
Credit for the original driver goes to Harry Gulliford and its contributors; credit for the dialect 1 adaptations goes to onezerotrash and its contributors.
The gcdsm12 fork adds the Laravel 12/13 adaptations and compatibility checks. Original Git history and attribution are retained.

The Composer package is named gabrielchagas/laravel-firebird. The existing PHP namespace onezerotrash\Firebird is intentionally preserved for compatibility; it does not imply that upstream maintains this fork.

## Requirements and validation

| Component | Requirement / status |
| --- | --- |
| Laravel | 12.x or 13.x |
| PHP | 8.2+ for Laravel 12; 8.3+ for Laravel 13 |
| Extensions | PDO and pdo_firebird |
| Firebird | Legacy 2.5 SQL is the primary compatibility target; support for 3/4/5 is inherited from upstream and requires integration verification |
| Dialects | 1 and 3; SQL compilation tested for both |

Unit tests run without database connections. They check grammar construction, prefixes, pagination including limit(0), bindings, procedures, dates/times, schema helper construction, DSN options and lazy provider resolution.
Local results (PHP 8.3.35): Laravel 12.72.0 / PHPUnit 11.5.57 and Laravel 13.42.0 / PHPUnit 12.5.43 each passed 6 unit tests / 25 assertions. The 80 historical integration tests are gated and were not run against a live database; the integration CI matrix still needs to run on GitHub. Unit compatibility tests are local-only in tests/Unit (ignored by Git); the CI workflow uses the tracked integration suite.

Passing unit tests does not certify a live Firebird server. Integration tests require a disposable database; never point them at a customer database.

## Changes in this fork

- Pass the connection into current Laravel query/schema grammar constructors.
- Remove the old withTablePrefix call; grammar prefix handling comes from the connection.
- Adapt hasTable SQL to the scalar result expected by Laravel 12/13 and preserve legacy column listing.
- Compile whereDate/whereTime using CAST instead of invalid EXTRACT(DATE/TIME) expressions.
- Preserve FIRST/SKIP pagination and ensure limit(0) cannot become an unrestricted query.
- Update PHPUnit metadata and integration CI for Laravel 12/13.

## Installation

After this fork is published on Packagist and a release is tagged:

    composer require gabrielchagas/laravel-firebird:^0.1

Until then, add a VCS repository for https://github.com/gcdsm12/laravel-firebird.git to your application Composer repositories and require gabrielchagas/laravel-firebird:dev-main after the changes have been pushed.
A local path repository may be used to test unpublished changes. Do not deploy an application lock file that depends on a local development path.
Do not install another Firebird driver registering the same driver name alongside this package.

## Configuration

The provider is discovered automatically. Use firebird as the driver:

```php
'firebird' => [
    'driver' => 'firebird',
    'host' => env('DB_FIREBIRD_HOST', '127.0.0.1'),
    'port' => env('DB_FIREBIRD_PORT', '3050'),
    'database' => env('DB_FIREBIRD_DATABASE', '/path/to/database.fdb'),
    'username' => env('DB_FIREBIRD_USERNAME'),
    'password' => env('DB_FIREBIRD_PASSWORD'),
    'charset' => env('DB_FIREBIRD_CHARSET', 'UTF8'),
    'dialect' => env('DB_FIREBIRD_DIALECT', '3'),
    'options' => [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ],
],
```

Keep existing connection names, charset, dialect and credentials when replacing a driver. CASE_NATURAL preserves database field names; use CASE_LOWER only if the application expects lowercase attributes. Never commit credentials.

## Tests

    composer install
    composer test # local unit suite, when tests/Unit is available

The historical integration suite creates/drops tables and a procedure. It requires an explicitly disposable Firebird database:

    FIREBIRD_TEST_DISPOSABLE=1 composer test:integration

Set DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD and DB_CHARSET for that disposable database. The environment variable is an explicit opt-in, not a substitute for checking the target.

## Limitations

This package does not provide general Laravel migrations/schema-management support. Legacy hasTable/column-listing compatibility does not change that limitation.
Features added to newer Laravel query builders are not automatically supported by Firebird 2.5.
Real customer databases have not been used for destructive integration tests.

## Publishing on Packagist

1. Review and push the fork changes to gcdsm12/laravel-firebird.
2. Run the local unit checks and the disposable-database integration matrix.
3. Tag a release, for example v0.1.0, then submit the GitHub repository to Packagist.
4. Check the package metadata and install the tagged release in the consuming application.

Publishing, pushing and tagging are left to the repository owner. No fixed version field is needed in composer.json: Composer uses Git tags.

## License

MIT, as declared by the upstream packages. Original authors and contributors retain their rights. See [LICENSE](LICENSE).

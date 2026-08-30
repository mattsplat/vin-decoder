# Contributing

Thanks for your interest in improving `mattsplat/vin-decode`.

## Getting set up

```bash
git clone git@github.com:mattsplat/vin-decoder.git
cd vin-decoder
composer install
```

## Before you open a PR

Run the full check suite locally — CI runs the same thing on PHP 8.1 through 8.4:

```bash
composer lint      # Pint, code style (composer format to auto-fix)
composer analyse   # PHPStan static analysis
composer test      # PHPUnit
```

## Guidelines

- **One logical change per PR.** Keep the diff focused.
- **Tests are required** for any behavioural change. HTTP is mocked with Guzzle's
  `MockHandler`; response fixtures live in `tests/fixtures/` and are captured
  verbatim from real vPIC API responses. When adding an endpoint, add a fixture
  and a test that asserts the DTO mapping.
- **Document new endpoints** in the relevant `docs/` page and the README method
  reference table.
- **Update `CHANGELOG.md`** under `## [Unreleased]`.
- Follow the existing code style (PSR-12 via Pint). Don't hand-format.

## Reporting bugs

Open an issue with the VIN or request parameters used, the response you got, and
the response you expected. Do not include personal data in issues.

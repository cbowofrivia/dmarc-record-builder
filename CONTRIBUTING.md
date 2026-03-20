# Contributing

Contributions are welcome. Please take a moment to read these guidelines before opening an issue or submitting a pull request.

## Reporting Bugs

Use the [bug report template](https://github.com/cbowofrivia/dmarc-record-builder/issues/new?template=bug_report.yml) to report reproducible bugs. Include a minimal DMARC record string or code snippet that demonstrates the problem.

## Suggesting Features

Open a [discussion](https://github.com/cbowofrivia/dmarc-record-builder/discussions/new?category=ideas) before submitting a feature PR. This avoids wasted effort if the idea isn't a good fit.

## Pull Requests

1. Fork the repository and create your branch from `main`.
2. Write or update tests to cover your change.
3. Ensure the full test suite passes.
4. Ensure code style passes (CI will auto-fix via Pint on your PR, but it is good practice to run it locally first).
5. Update `CHANGELOG.md` under an `Unreleased` heading, following the existing format.
6. Open a pull request against `main`.

Keep PRs focused. One concern per PR makes review faster and history easier to follow.

## Local Setup

Clone the repository and install dependencies:

```bash
git clone https://github.com/cbowofrivia/dmarc-record-builder.git
cd dmarc-record-builder
composer install
```

## Running Tests

```bash
composer test
```

The test suite runs against PHP 8.2, 8.3, and 8.4 on both `prefer-stable` and `prefer-lowest` dependency sets in CI. If your change targets a specific PHP version, note it in your PR.

## Code Style

This project uses [Laravel Pint](https://github.com/laravel/pint) for code formatting. CI will automatically apply fixes to your PR branch, but you can run it locally before pushing:

```bash
vendor/bin/pint
```

## Static Analysis

[Rector](https://github.com/rectorphp/rector) is available for automated refactoring. Run it locally if you want to check for improvements:

```bash
vendor/bin/rector --dry-run
```

## Versioning

This project follows [Semantic Versioning](https://semver.org/):

- **Patch** — backwards-compatible bug fixes
- **Minor** — backwards-compatible new functionality
- **Major** — breaking changes

If your change is breaking (e.g. a method signature change, property type change, or removed feature), say so clearly in your PR description. Breaking changes require a major version bump and a migration guide in `CHANGELOG.md`.

# Contributing

Contributions are welcome. Please take a moment to read these guidelines before opening an issue or submitting a pull request.

## Reporting Bugs

Use the [bug report template](https://github.com/cbowofrivia/dmarc-record-builder/issues/new?template=bug_report.yml) to report reproducible bugs. Include a minimal code snippet or DMARC record string that demonstrates the problem.

## Suggesting Features

Open a [discussion](https://github.com/cbowofrivia/dmarc-record-builder/discussions/new?category=ideas) before submitting a feature PR. This avoids wasted effort if the idea isn't a good fit.

## Pull Requests

1. Fork the repository and create your branch from `main`.
2. Write or update tests to cover your change.
3. Ensure the full test suite passes (`composer test`).
4. Update [CHANGELOG.md](CHANGELOG.md) — add a brief entry under the relevant version or at the top.
5. Open a pull request against `main`.

CI will automatically apply Pint code style fixes to your branch. You can also run it locally with `vendor/bin/pint`.

## Local Setup

```bash
git clone https://github.com/cbowofrivia/dmarc-record-builder.git
cd dmarc-record-builder
composer install
```

## Versioning

This project follows [Semantic Versioning](https://semver.org/). If your change is breaking (method signature change, property type change, removed feature), say so clearly in your PR. Breaking changes require a major version bump and a migration note in `CHANGELOG.md`.

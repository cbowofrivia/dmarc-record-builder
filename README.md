# DMARC Record Builder

[![Latest Version on Packagist](https://img.shields.io/packagist/v/cbowofrivia/dmarc-record-builder.svg?style=flat-square)](https://packagist.org/packages/cbowofrivia/dmarc-record-builder)
[![Tests](https://github.com/cbowofrivia/dmarc-record-builder/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/cbowofrivia/dmarc-record-builder/actions/workflows/run-tests.yml)
[![codecov](https://codecov.io/gh/cbowofrivia/dmarc-record-builder/graph/badge.svg)](https://codecov.io/gh/cbowofrivia/dmarc-record-builder)
[![Total Downloads](https://img.shields.io/packagist/dt/cbowofrivia/dmarc-record-builder.svg?style=flat-square)](https://packagist.org/packages/cbowofrivia/dmarc-record-builder)

A PHP package for building and parsing DMARC DNS records with a fluent, human-friendly API. Accepts readable values (`'relaxed'`, `'strict'`, `'reject'`) and outputs correctly formatted DMARC strings.

## Requirements

- PHP 8.3 or higher

This package has no runtime dependencies.

## Installation

```bash
composer require cbowofrivia/dmarc-record-builder
```

## Quick Start

```php
use CbowOfRivia\DmarcRecordBuilder\DmarcRecord;

$record = (string) (new DmarcRecord())
    ->policy('reject')
    ->rua('mailto:dmarc@example.com');

// v=DMARC1; p=reject; rua=mailto:dmarc@example.com
```

## Building a Record

### Fluent API

All setter methods return `$this`, so calls can be chained:

```php
$record = new DmarcRecord();

$record->policy('reject')
    ->subdomainPolicy('quarantine')
    ->rua('mailto:dmarc@example.com')
    ->ruf('mailto:dmarc-forensic@example.com')
    ->adkim('relaxed')
    ->aspf('strict')
    ->reporting(['dkim', 'spf'])
    ->nonExistentSubdomainPolicy('reject')
    ->publicSuffixDomainPolicy('y')
    ->testingMode('n');

echo $record;
// v=DMARC1; p=reject; sp=quarantine; rua=mailto:dmarc@example.com; ruf=mailto:dmarc-forensic@example.com; adkim=r; aspf=s; fo=d:s; np=reject; psd=y; t=n
```

### Constructor

All parameters are optional. The constructor accepts the same values as the fluent setters:

```php
$record = new DmarcRecord(
    version: 'DMARC1',
    policy: 'reject',
    subdomain_policy: 'quarantine',
    rua: 'mailto:dmarc@example.com',
    ruf: 'mailto:dmarc-forensic@example.com',
    adkim: 'relaxed',
    aspf: 'strict',
    reporting: ['dkim', 'spf'],
    np: 'reject',
    psd: 'y',
    t: 'n',
);
```

### Static Factory

`DmarcRecord::create()` is a convenience wrapper around the constructor, useful when you want to build and cast in one expression:

```php
$record = (string) DmarcRecord::create(
    policy: 'quarantine',
    rua: 'mailto:dmarc@example.com',
);

// v=DMARC1; p=quarantine; rua=mailto:dmarc@example.com
```

## Parsing an Existing Record

Pass a raw DMARC TXT record string to `DmarcRecord::parse()` to get a populated `DmarcRecord` object. This is useful for reading and modifying existing records.

```php
$record = DmarcRecord::parse('v=DMARC1; p=quarantine; adkim=r; aspf=s; fo=d:s');

$record->policy;      // 'quarantine'
$record->adkim;       // 'relaxed'  (translated from 'r')
$record->aspf;        // 'strict'   (translated from 's')
$record->reporting;   // ['dkim', 'spf']  (translated from 'fo=d:s')
```

After parsing, you can modify the record and re-cast it to a string:

```php
$record = DmarcRecord::parse('v=DMARC1; p=none; rua=mailto:dmarc@example.com');
$record->policy('reject');

echo $record;
// v=DMARC1; p=reject; rua=mailto:dmarc@example.com
```

`parse()` requires both `v` and `p` tags to be present and will throw an `InvalidDmarcRecordException` if either is missing. Unknown tags are silently ignored.

## Tag Reference

| Method | DMARC Tag | Accepted Values | Default |
|---|---|---|---|
| `version()` | `v` | `'DMARC1'` | `'DMARC1'` |
| `policy()` | `p` | `'none'`, `'quarantine'`, `'reject'` | `'none'` |
| `subdomainPolicy()` | `sp` | `'none'`, `'quarantine'`, `'reject'` | `null` |
| `rua()` | `rua` | `'mailto:...'` | `null` |
| `ruf()` | `ruf` | `'mailto:...'` | `null` |
| `adkim()` | `adkim` | `'relaxed'`, `'strict'` | `null` |
| `aspf()` | `aspf` | `'relaxed'`, `'strict'` | `null` |
| `reporting()` | `fo` | `'all'`, `'any'`, `'dkim'`, `'spf'` | `[]` |
| `nonExistentSubdomainPolicy()` | `np` | `'none'`, `'quarantine'`, `'reject'` | `null` |
| `publicSuffixDomainPolicy()` | `psd` | `'y'`, `'n'`, `'u'` | `null` |
| `testingMode()` | `t` | `'y'`, `'n'` | `null` |

Tags with a `null` value are omitted from the output string. Only `v` and `p` are always emitted.

> **Note:** RFC 9989 (DMARCbis) removed the `pct` and `ri` tags, so this package no longer supports them (removed in `4.0.0`). Records that still contain those tags parse without error — the tags are simply ignored.

### Tag Details

#### `policy()` / `subdomainPolicy()` / `nonExistentSubdomainPolicy()`

Controls how the receiving mail server handles messages that fail DMARC checks.

- `'none'` — take no action; useful during monitoring
- `'quarantine'` — send to spam/junk
- `'reject'` — reject the message outright

`subdomainPolicy()` (`sp`) overrides `policy()` for subdomains. If omitted, subdomains inherit the main policy.

`nonExistentSubdomainPolicy()` (`np`) applies to non-existent subdomains (RFC 9091 / DMARCbis). Takes precedence over both `policy()` and `subdomainPolicy()` for those domains.

#### `rua()` / `ruf()`

URIs for receiving DMARC reports. Must be prefixed with `mailto:`.

- `rua` — aggregate reports (daily summaries from receivers)
- `ruf` — forensic/failure reports (per-message failure details; not all receivers send these)

```php
->rua('mailto:dmarc@example.com')
->ruf('mailto:dmarc-forensic@example.com')
```

Both tags also accept an array of addresses (RFC 9989 — comma-separated list):

```php
->rua(['mailto:dmarc@example.com', 'mailto:backup@example.com'])
// rua=mailto:dmarc@example.com,mailto:backup@example.com
```

#### `adkim()` / `aspf()`

Alignment mode for DKIM and SPF respectively.

- `'relaxed'` — the organisational domain must match (e.g. `mail.example.com` aligns with `example.com`)
- `'strict'` — the domains must match exactly

Omitting either defaults to relaxed alignment per the RFC.

#### `reporting()`

Specifies which failure conditions trigger a forensic report. Accepts a string or an array of options:

| Value | `fo` tag | Meaning |
|---|---|---|
| `'all'` | `fo=0` | Report if all mechanisms fail |
| `'any'` | `fo=1` | Report if any mechanism fails |
| `'dkim'` | `fo=d` | Report if DKIM fails |
| `'spf'` | `fo=s` | Report if SPF fails |

Multiple values produce a colon-separated `fo` tag:

```php
->reporting(['dkim', 'spf'])
// fo=d:s

->reporting(['any'])
// fo=1
```

Duplicate values are silently deduplicated. `'all'` and `'any'` are mutually exclusive — passing both throws an `InvalidDmarcRecordException`.

#### `publicSuffixDomainPolicy()`

Controls DMARC policy application at public suffix domains (DMARCbis extension).

- `'y'` — apply DMARC policy to this public suffix domain
- `'n'` — do not apply policy to this public suffix domain
- `'u'` — undefined / unknown

#### `testingMode()`

When set to `'y'`, signals that DMARC is in testing mode. Receivers should not apply policy actions but may still send reports.

## Validation

The package validates inputs on each setter call. Passing an invalid value throws a `CbowOfRivia\DmarcRecordBuilder\Exceptions\InvalidDmarcRecordException`. It extends the native `\InvalidArgumentException`, so existing `catch (\InvalidArgumentException)` handlers continue to work.

```php
// Throws: Expected one of: "none", "quarantine", "reject", null. Got: "monitor"
$record->policy('monitor');

// Throws: rua mailto address should start with "mailto:"
$record->rua('dmarc@example.com');

// Throws: Expected one of: "relaxed", "strict", null. Got: "loose"
$record->adkim('loose');

// Throws: Reporting options "all" (0) and "any" (1) are mutually exclusive.
$record->reporting(['all', 'any']);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Charles Bowen](https://github.com/cbowofrivia)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

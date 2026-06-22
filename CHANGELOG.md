# Changelog

All notable changes to `dmarc-record-builder` will be documented in this file.

## 4.0.0 - 2026-06-22

### Breaking Changes

- **Removed the `pct()` and `interval()` methods** — RFC 9989 (DMARCbis) removed the `pct` and `ri` tags. The methods, their constructor/`create()` arguments, and the `$pct` / `$interval` properties are gone. `parse()` now treats `pct` and `ri` as unknown tags: existing records still parse without error and those tags are dropped on re-cast.
- **Minimum PHP raised to 8.3** (was 8.2, which reaches end-of-life in December 2026).
- **Validation exception type changed** — validation now throws `CbowOfRivia\DmarcRecordBuilder\Exceptions\InvalidDmarcRecordException` instead of `Webmozart\Assert\InvalidArgumentException`. The new exception extends `\InvalidArgumentException`, so `catch (\InvalidArgumentException)` continues to work; exception messages are unchanged.

### Changed

- **Removed all runtime dependencies** — dropped `webmozart/assert` and `illuminate/collections`. The package no longer pins consumers to a Laravel major or installs transitive packages. Validation is handled by internal static guards on `InvalidDmarcRecordException`, and `parse()` uses plain PHP arrays.

### Migration Guide

```php
// Removed — delete these calls
$record->pct(100);
$record->interval(86400);
DmarcRecord::create(pct: 100, interval: 86400);
$record->pct;       // property removed
$record->interval;  // property removed

// Catching validation errors — use the native parent or the new exception
use CbowOfRivia\DmarcRecordBuilder\Exceptions\InvalidDmarcRecordException;

try {
    $record->policy('invalid');
} catch (InvalidDmarcRecordException $e) {  // or \InvalidArgumentException
    // ...
}
```

- Require PHP 8.3 or higher.

## 3.1.0 - 2026-06-18

### Added

- **`rua()` and `ruf()` now accept an array of addresses** in addition to a single string, supporting the comma-separated URI list defined in RFC 9989 (DMARCbis). Each address is validated to start with `mailto:`; the value is stored and emitted as a comma-separated list (e.g. `rua=mailto:a@example.com,mailto:b@example.com`). Single-string usage is unchanged.

### Deprecated

- **`pct()` and `interval()`** — RFC 9989 (DMARCbis) removed the `pct` and `ri` tags. Both methods continue to work unchanged and are retained for backwards compatibility, but are scheduled for removal in `4.0.0`.

## 3.0.0 - 2026-03-20

### Breaking Changes

- **`$reporting` property type changed from `?string` to `array`** — the property now holds a list of failure reporting options (e.g. `['dkim', 'spf']`) rather than a single string. An empty array means the tag is absent from the record.
- **`reporting()` method signature changed** — now accepts `array $values = []` instead of `?string $value`. Pass an array of valid options (`'all'`, `'any'`, `'dkim'`, `'spf'`); pass `[]` to clear the value.
- **`create()` and constructor `$reporting` parameter type changed** from `?string` to `array`.

### Bug Fixes

- Fixed incorrect DMARC tag name: `ro` has been corrected to `fo` (RFC 7489 §6.3). Records parsed with the old `ro` tag were silently ignored; generated records emitted an invalid tag that receivers would ignore.
- The `fo` tag now correctly supports multiple colon-separated values as defined in RFC 7489 §6.3 (e.g. `fo=d:s` for DKIM and SPF failure reporting). Previously only a single value was accepted.

### Migration Guide

```php
// Before (v2.x)
$record->reporting('dkim');
// $record->reporting === 'dkim'

// After (v3.0)
$record->reporting(['dkim']);
// $record->reporting === ['dkim']

// Multiple options (new in v3.0)
$record->reporting(['dkim', 'spf']);
// Outputs: fo=d:s

// Clear the value
$record->reporting([]);
```

## 1.0.0 - 2022-05-06
- Initial release

# Changelog

All notable changes to `dmarc-record-builder` will be documented in this file.

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

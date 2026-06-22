<?php

declare(strict_types=1);

namespace CbowOfRivia\DmarcRecordBuilder\Support;

use CbowOfRivia\DmarcRecordBuilder\Exceptions\InvalidDmarcRecordException;

/**
 * Minimal internal assertion helper, replacing the few webmozart/assert
 * methods the builder used. Default messages mirror webmozart's wording so
 * callers matching on exception text are unaffected.
 *
 * @internal
 */
final class Assert
{
    /**
     * @param  array<int, mixed>  $values
     */
    public static function inArray(mixed $value, array $values, string $message = ''): void
    {
        if (! in_array($value, $values, true)) {
            throw new InvalidDmarcRecordException($message !== '' ? $message : sprintf(
                'Expected one of: %2$s. Got: %s',
                self::valueToString($value),
                implode(', ', array_map([self::class, 'valueToString'], $values)),
            ));
        }
    }

    /**
     * @param  iterable<mixed>  $value
     * @param  array<int, mixed>  $values
     */
    public static function allInArray(iterable $value, array $values, string $message = ''): void
    {
        foreach ($value as $entry) {
            self::inArray($entry, $values, $message);
        }
    }

    public static function startsWith(string $value, string $prefix, string $message = ''): void
    {
        if (! str_starts_with($value, $prefix)) {
            throw new InvalidDmarcRecordException($message !== '' ? $message : sprintf(
                'Expected a value to start with %2$s. Got: %s',
                self::valueToString($value),
                self::valueToString($prefix),
            ));
        }
    }

    public static function false(mixed $value, string $message = ''): void
    {
        if ($value !== false) {
            throw new InvalidDmarcRecordException($message !== '' ? $message : sprintf(
                'Expected a value to be false. Got: %s',
                self::valueToString($value),
            ));
        }
    }

    /**
     * @param  array<array-key, mixed>  $array
     */
    public static function keyExists(array $array, int|string $key, string $message = ''): void
    {
        if (! array_key_exists($key, $array)) {
            throw new InvalidDmarcRecordException($message !== '' ? $message : sprintf(
                'Expected the key %s to exist.',
                self::valueToString($key),
            ));
        }
    }

    private static function valueToString(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        if (is_array($value)) {
            return 'array';
        }

        if (is_object($value)) {
            return get_class($value);
        }

        if (is_string($value)) {
            return '"'.$value.'"';
        }

        return (string) $value;
    }
}

<?php

declare(strict_types=1);

namespace CbowOfRivia\DmarcRecordBuilder\Support;

use CbowOfRivia\DmarcRecordBuilder\Exceptions\InvalidDmarcRecordException;

/**
 * Minimal internal assertion helper, replacing the few webmozart/assert
 * methods the builder used. The inArray message mirrors webmozart's wording
 * so callers matching on exception text are unaffected.
 *
 * @internal
 */
final class Assert
{
    /**
     * @param  array<int, mixed>  $values
     */
    public static function inArray(mixed $value, array $values): void
    {
        if (! in_array($value, $values, true)) {
            throw new InvalidDmarcRecordException(sprintf(
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
    public static function allInArray(iterable $value, array $values): void
    {
        foreach ($value as $entry) {
            self::inArray($entry, $values);
        }
    }

    public static function startsWith(string $value, string $prefix, string $message): void
    {
        if (! str_starts_with($value, $prefix)) {
            throw new InvalidDmarcRecordException($message);
        }
    }

    public static function false(mixed $value, string $message): void
    {
        if ($value !== false) {
            throw new InvalidDmarcRecordException($message);
        }
    }

    /**
     * @param  array<array-key, mixed>  $array
     */
    public static function keyExists(array $array, int|string $key, string $message): void
    {
        if (! array_key_exists($key, $array)) {
            throw new InvalidDmarcRecordException($message);
        }
    }

    private static function valueToString(mixed $value): string
    {
        return $value === null ? 'null' : '"'.$value.'"';
    }
}

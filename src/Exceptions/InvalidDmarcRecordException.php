<?php

declare(strict_types=1);

namespace CbowOfRivia\DmarcRecordBuilder\Exceptions;

use InvalidArgumentException;

/**
 * Thrown when a tag is given a disallowed value, or a required tag is missing
 * while parsing. Extends \InvalidArgumentException so existing
 * `catch (\InvalidArgumentException)` handlers keep working.
 *
 * The static guards validate input and throw on failure. The inArray message
 * mirrors the wording emitted before 4.0, so callers matching on exception
 * text are unaffected.
 */
class InvalidDmarcRecordException extends InvalidArgumentException
{
    /**
     * @param  array<int, mixed>  $allowed
     */
    public static function inArray(mixed $value, array $allowed): void
    {
        if (! in_array($value, $allowed, true)) {
            throw new self(sprintf(
                'Expected one of: %2$s. Got: %s',
                self::stringify($value),
                implode(', ', array_map([self::class, 'stringify'], $allowed)),
            ));
        }
    }

    /**
     * @param  iterable<mixed>  $values
     * @param  array<int, mixed>  $allowed
     */
    public static function allInArray(iterable $values, array $allowed): void
    {
        foreach ($values as $value) {
            self::inArray($value, $allowed);
        }
    }

    public static function startsWith(string $value, string $prefix, string $message): void
    {
        if (! str_starts_with($value, $prefix)) {
            throw new self($message);
        }
    }

    /**
     * @param  array<array-key, mixed>  $array
     */
    public static function keyExists(array $array, int|string $key, string $message): void
    {
        if (! array_key_exists($key, $array)) {
            throw new self($message);
        }
    }

    public static function throwIf(bool $condition, string $message): void
    {
        if ($condition) {
            throw new self($message);
        }
    }

    private static function stringify(mixed $value): string
    {
        return $value === null ? 'null' : '"'.$value.'"';
    }
}

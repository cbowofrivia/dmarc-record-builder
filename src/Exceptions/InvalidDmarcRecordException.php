<?php

declare(strict_types=1);

namespace CbowOfRivia\DmarcRecordBuilder\Exceptions;

use InvalidArgumentException;

/**
 * Thrown when a tag is given a disallowed value, or a required tag is missing
 * while parsing. Extends \InvalidArgumentException so existing
 * `catch (\InvalidArgumentException)` handlers keep working.
 */
class InvalidDmarcRecordException extends InvalidArgumentException {}

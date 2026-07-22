<?php

namespace App\Features\CumulativeNetValue\Exceptions;

use RuntimeException;
use Throwable;

final class CumulativeNetValueException extends RuntimeException
{
    public function __construct(
        string $message = 'Failed to get cumulative net value',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}

<?php

namespace App\Features\ForeignFlowVsNetValue\Exceptions;

use RuntimeException;
use Throwable;

final class ForeignFlowVsNetValueException extends RuntimeException
{
    public function __construct(
        string $message = 'Failed to get foreign flow vs net value data',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}

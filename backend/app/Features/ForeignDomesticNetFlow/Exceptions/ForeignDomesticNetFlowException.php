<?php

namespace App\Features\ForeignDomesticNetFlow\Exceptions;

use RuntimeException;
use Throwable;

final class ForeignDomesticNetFlowException extends RuntimeException
{
    public function __construct(
        string $message = 'Failed to get foreign vs domestic net flow',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}

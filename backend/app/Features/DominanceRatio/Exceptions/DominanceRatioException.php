<?php

namespace App\Features\DominanceRatio\Exceptions;

use RuntimeException;
use Throwable;

final class DominanceRatioException extends RuntimeException
{
    public function __construct(
        string $message = 'Failed to get dominance ratio',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}

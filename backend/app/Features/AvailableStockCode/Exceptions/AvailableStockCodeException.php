<?php

namespace App\Features\AvailableStockCode\Exceptions;

use RuntimeException;
use Throwable;

final class AvailableStockCodeException extends RuntimeException
{
    public function __construct(
        string $message = 'Failed to get available stock codes',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}

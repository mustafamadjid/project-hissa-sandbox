<?php

namespace App\Features\ForeignBuyVsSell\Exceptions;

use RuntimeException;
use Throwable;

final class ForeignBuyVsSellException extends RuntimeException
{
    public function __construct(
        string $message = 'Failed to get foreign gross flow',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
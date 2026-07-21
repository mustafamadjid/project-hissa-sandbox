<?php

namespace App\Features\TopAccumulationDistribution\Exceptions;

use RuntimeException;
use Throwable;

final class TopAccumulationDistributionException extends RuntimeException
{
    public function __construct(
        string $message = 'Failed to get top accumulation distribution',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}

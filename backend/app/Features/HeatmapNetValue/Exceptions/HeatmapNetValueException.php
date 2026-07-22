<?php

namespace App\Features\HeatmapNetValue\Exceptions;

use RuntimeException;
use Throwable;

final class HeatmapNetValueException extends RuntimeException
{
    public function __construct(
        string $message = 'Failed to get heatmap net value',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
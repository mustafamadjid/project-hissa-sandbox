<?php

namespace App\Support\Observability;

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;

final class PerformanceTracker
{
    public static function isEnabled(): bool
    {
        return (bool) config('observability.performance.enabled', true);
    }

    public static function measure(string $operation, callable $callback, array $context = []): mixed
    {
        if (! self::isEnabled()) {
            return $callback();
        }

        $traceId = Context::get('trace_id');
        $start = hrtime(true);

        try {
            $result = $callback();

            self::log($operation, $start, $traceId, $context);

            return $result;
        } catch (\Throwable $e) {
            self::log($operation, $start, $traceId, $context, $e);

            throw $e;
        }
    }

    private static function log(string $operation, int|float $start, ?string $traceId, array $context, ?\Throwable $exception = null): void
    {
        $durationMs = (hrtime(true) - $start) / 1_000_000;
        $threshold = (int) config('observability.performance.threshold_ms', 100);
        $level = $durationMs >= $threshold ? 'warning' : 'debug';

        $logContext = array_merge([
            'trace_id' => $traceId,
            'operation' => $operation,
            'duration_ms' => round($durationMs, 2),
            'exception' => $exception ? $exception::class : null,
        ], $context);

        Log::channel('observability')->log($level, 'Operation traced', $logContext);
    }
}

<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('ip', function (Request $request) {
            return Limit::perSecond(10)->by($request->ip());
        });

        DB::listen(function (QueryExecuted $query): void {
            if (! config('observability.sql.enabled', true)) {
                return;
            }

            $traceId = Context::get('trace_id');
            $timeMs = (float) $query->time;
            $threshold = (int) config('observability.sql.threshold_ms', 100);
            $level = $timeMs >= $threshold ? 'warning' : 'debug';

            Log::channel('observability')->log($level, 'SQL query executed', [
                'trace_id' => $traceId,
                'sql' => $query->sql,
                'bindings' => array_map(
                    fn (mixed $binding): mixed => is_string($binding) && self::isSensitive($binding) ? '[REDACTED]' : $binding,
                    $query->bindings,
                ),
                'time_ms' => $timeMs,
                'connection' => $query->connectionName,
            ]);
        });
    }

    private static function isSensitive(string $value): bool
    {
        $lower = strtolower($value);

        return strlen($value) > 64
            || str_contains($lower, 'password')
            || str_contains($lower, 'token')
            || str_contains($lower, 'secret')
            || str_contains($lower, 'bearer')
            || str_contains($lower, 'api_key');
    }
}

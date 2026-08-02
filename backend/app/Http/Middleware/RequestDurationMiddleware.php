<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class RequestDurationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $traceId = (string) Str::uuid();
        Context::add('trace_id', $traceId);

        $start = hrtime(true);
        $exception = null;

        try {
            $response = $next($request);
            $statusCode = $response->getStatusCode();
        } catch (\Throwable $e) {
            $exception = $e;
            $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;
        }

        $durationMs = (hrtime(true) - $start) / 1_000_000;

        Log::channel('observability')->info('HTTP request completed', [
            'trace_id' => $traceId,
            'method' => $request->method(),
            'route' => $request->route()?->getName() ?? 'unnamed',
            'path' => $request->path(),
            'status' => $statusCode,
            'duration_ms' => round($durationMs, 2),
            'exception' => $exception ? $exception::class : null,
        ]);

        if (isset($response)) {
            $response->headers->set('X-Trace-Id', $traceId);
        }

        if ($exception) {
            throw $exception;
        }

        return $response;
    }
}

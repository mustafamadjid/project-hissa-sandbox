<?php

use App\Http\Middleware\RequestDurationMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function (): void {
    Context::flush();
});

it('adds x-trace-id header to response', function (): void {
    $middleware = new RequestDurationMiddleware;

    Log::shouldReceive('channel')->with('observability')->andReturnSelf();
    Log::shouldReceive('info')->once();

    $response = $middleware->handle(
        Request::create('/test', 'GET'),
        fn (): Response => response('ok', 200),
    );

    expect($response->headers->get('X-Trace-Id'))->toBeString()->not->toBeEmpty();
    expect(Context::get('trace_id'))->toBe($response->headers->get('X-Trace-Id'));
});

it('logs duration for successful request', function (): void {
    $middleware = new RequestDurationMiddleware;

    Log::shouldReceive('channel')
        ->once()
        ->with('observability')
        ->andReturnSelf();

    Log::shouldReceive('info')
        ->once()
        ->withArgs(function (string $message, array $context): bool {
            return $message === 'HTTP request completed'
                && $context['status'] === 200
                && $context['method'] === 'GET'
                && $context['path'] === 'test'
                && $context['duration_ms'] >= 0
                && $context['exception'] === null
                && is_string($context['trace_id']);
        });

    $middleware->handle(
        Request::create('/test', 'GET'),
        fn (): Response => response('ok', 200),
    );
});

it('logs duration when exception occurs and rethrows', function (): void {
    $middleware = new RequestDurationMiddleware;

    Log::shouldReceive('channel')
        ->once()
        ->with('observability')
        ->andReturnSelf();

    Log::shouldReceive('info')
        ->once()
        ->withArgs(function (string $message, array $context): bool {
            return $message === 'HTTP request completed'
                && $context['status'] === 500
                && $context['exception'] === RuntimeException::class
                && $context['duration_ms'] >= 0;
        });

    expect(fn () => $middleware->handle(
        Request::create('/boom', 'GET'),
        fn () => throw new RuntimeException('boom'),
    ))->toThrow(RuntimeException::class, 'boom');
});

it('generates unique trace id per request', function (): void {
    $middleware = new RequestDurationMiddleware;

    Log::shouldReceive('channel')->with('observability')->andReturnSelf();
    Log::shouldReceive('info')->twice();

    $first = $middleware->handle(
        Request::create('/a', 'GET'),
        fn (): Response => response('ok'),
    );

    Context::flush();

    $second = $middleware->handle(
        Request::create('/b', 'GET'),
        fn (): Response => response('ok'),
    );

    expect($first->headers->get('X-Trace-Id'))
        ->not->toBe($second->headers->get('X-Trace-Id'));
});

it('does not log sensitive request data', function (): void {
    $middleware = new RequestDurationMiddleware;

    Log::shouldReceive('channel')->with('observability')->andReturnSelf();
    Log::shouldReceive('info')
        ->once()
        ->withArgs(function (string $message, array $context): bool {
            $json = json_encode($context);

            return ! str_contains((string) $json, 'Bearer secret')
                && ! str_contains((string) $json, 'password')
                && ! str_contains((string) $json, 'session=secret');
        });

    $request = Request::create('/secure', 'POST', ['password' => 'secret']);
    $request->headers->set('Authorization', 'Bearer secret');
    $request->headers->set('Cookie', 'session=secret');

    $middleware->handle($request, fn (): Response => response('ok'));
});

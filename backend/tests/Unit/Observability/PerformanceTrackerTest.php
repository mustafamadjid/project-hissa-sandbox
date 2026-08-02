<?php

use App\Support\Observability\PerformanceTracker;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\Log;

beforeEach(function (): void {
    Context::flush();
    config([
        'observability.performance.enabled' => true,
        'observability.performance.threshold_ms' => 100,
    ]);
});

it('returns callback result', function (): void {
    Log::shouldReceive('channel')->with('observability')->andReturnSelf();
    Log::shouldReceive('log')->once();

    $result = PerformanceTracker::measure('TestOp@test', fn () => 42);

    expect($result)->toBe(42);
});

it('supports mixed return types', function (): void {
    Log::shouldReceive('channel')->with('observability')->andReturnSelf();
    Log::shouldReceive('log')->once();

    $payload = ['a' => 1];

    expect(PerformanceTracker::measure('TestOp@test', fn () => $payload))->toBe($payload);
});

it('does not swallow exceptions', function (): void {
    Log::shouldReceive('channel')->with('observability')->andReturnSelf();
    Log::shouldReceive('log')->once();

    expect(fn (): mixed => PerformanceTracker::measure(
        'TestOp@throws',
        fn () => throw new RuntimeException('boom'),
    ))->toThrow(RuntimeException::class, 'boom');
});

it('includes trace id from context', function (): void {
    Context::add('trace_id', 'abc-123');

    Log::shouldReceive('channel')
        ->once()
        ->with('observability')
        ->andReturnSelf();

    Log::shouldReceive('log')
        ->once()
        ->withArgs(function (string $level, string $message, array $context): bool {
            return $level === 'debug'
                && $message === 'Operation traced'
                && $context['trace_id'] === 'abc-123'
                && $context['operation'] === 'TestOp@test';
        });

    PerformanceTracker::measure('TestOp@test', fn () => null);
});

it('logs slow operations as warning', function (): void {
    config(['observability.performance.threshold_ms' => 0]);

    Log::shouldReceive('channel')->once()->with('observability')->andReturnSelf();
    Log::shouldReceive('log')
        ->once()
        ->withArgs(fn (string $level): bool => $level === 'warning');

    PerformanceTracker::measure('SlowOp@Test', fn () => null);
});

it('logs fast operations as debug', function (): void {
    config(['observability.performance.threshold_ms' => 5000]);

    Log::shouldReceive('channel')->once()->with('observability')->andReturnSelf();
    Log::shouldReceive('log')
        ->once()
        ->withArgs(fn (string $level): bool => $level === 'debug');

    PerformanceTracker::measure('FastOp@Test', fn () => null);
});

it('logs exception class without sensitive payload', function (): void {
    Log::shouldReceive('channel')->once()->with('observability')->andReturnSelf();
    Log::shouldReceive('log')
        ->once()
        ->withArgs(function (string $level, string $message, array $context): bool {
            return $context['exception'] === RuntimeException::class
                && ! isset($context['exception_message'])
                && ! isset($context['password']);
        });

    try {
        PerformanceTracker::measure(
            'FailingOp@test',
            fn () => throw new RuntimeException('secret password in message'),
        );
    } catch (RuntimeException) {
    }
});

it('can be disabled via config', function (): void {
    config(['observability.performance.enabled' => false]);

    Log::shouldReceive('channel')->never();
    Log::shouldReceive('log')->never();

    expect(PerformanceTracker::measure('DisabledOp@test', fn () => 'ok'))->toBe('ok');
});

it('records duration even on exception', function (): void {
    Log::shouldReceive('channel')->once()->with('observability')->andReturnSelf();
    Log::shouldReceive('log')
        ->once()
        ->withArgs(function (string $level, string $message, array $context): bool {
            return isset($context['duration_ms']) && $context['duration_ms'] >= 0;
        });

    try {
        PerformanceTracker::measure('Op@test', fn () => throw new RuntimeException);
    } catch (RuntimeException) {
    }
});

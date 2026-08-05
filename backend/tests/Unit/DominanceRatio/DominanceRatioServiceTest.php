<?php

use App\Features\DominanceRatio\Contracts\DominanceRatioContract;
use App\Features\DominanceRatio\Exceptions\DominanceRatioException;
use App\Features\DominanceRatio\Services\DominanceRatioService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

it('maps repository rows into timeline response with total ratio', function () {
    $repository = Mockery::mock(DominanceRatioContract::class);
    $repository->shouldReceive('getDominanceRatio')
        ->once()
        ->with('BBRI', '2026-07-01', '2026-07-17', 'daily')
        ->andReturn(collect([
            (object) ['date' => '2026-07-17', 'stock_code' => 'BBRI', 'institution' => '70.00', 'retail' => '20.00', 'mixed' => '10.00'],
        ]));
    App::instance(DominanceRatioContract::class, $repository);

    $result = App::make(DominanceRatioService::class)
        ->getDominanceRatio('BBRI', '2026-07-01', '2026-07-17', 'daily');

    expect($result)->toBe([
        'stock_code' => 'BBRI',
        'period' => ['start_date' => '2026-07-01', 'end_date' => '2026-07-17'],
        'granularity' => 'daily',
        'points' => [
            ['date' => '2026-07-17', 'institution_ratio' => 70.0, 'retail_ratio' => 20.0, 'mixed_ratio' => 10.0, 'total_ratio' => 100.0],
        ],
        'meta' => ['unit' => 'percent', 'ratio_basis' => 'transaction_value', 'aggregation' => 'daily', 'timezone' => 'Asia/Jakarta'],
    ]);
});

it('forwards timeline filters to repository', function () {
    $repository = Mockery::mock(DominanceRatioContract::class);
    $repository->shouldReceive('getDominanceRatio')
        ->once()
        ->with('BBRI', '2026-07-01', '2026-07-17', 'weekly')
        ->andReturn(collect());
    App::instance(DominanceRatioContract::class, $repository);

    $result = App::make(DominanceRatioService::class)
        ->getDominanceRatio('BBRI', '2026-07-01', '2026-07-17', 'weekly');

    expect($result['points'])->toBe([])
        ->and($result['meta']['aggregation'])->toBe('latest');
});

it('logs and wraps repository failures', function () {
    Log::spy();
    $repository = Mockery::mock(DominanceRatioContract::class);
    $repository->shouldReceive('getDominanceRatio')->once()->andThrow(new RuntimeException('DB down'));
    App::instance(DominanceRatioContract::class, $repository);

    App::make(DominanceRatioService::class)
        ->getDominanceRatio('BBRI', '2026-07-01', '2026-07-17', 'daily');
})->throws(DominanceRatioException::class, 'Failed to get dominance ratio');

<?php

use App\Features\DominanceRatio\Contracts\DominanceRatioContract;
use App\Features\DominanceRatio\Exceptions\DominanceRatioException;
use App\Features\DominanceRatio\Services\DominanceRatioService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

it('maps repository rows into dominance ratio items with total ratio', function () {
    $repository = Mockery::mock(DominanceRatioContract::class);
    $repository->shouldReceive('getDominanceRatio')
        ->once()
        ->with('2026-07-01', '2026-07-17', null)
        ->andReturn(collect([
            (object) ['date' => '2026-07-17', 'stock_code' => 'BBRI', 'institution' => '70.00', 'retail' => '20.00', 'mixed' => '10.00'],
        ]));
    App::instance(DominanceRatioContract::class, $repository);

    $result = App::make(DominanceRatioService::class)
        ->getDominanceRatio('2026-07-01', '2026-07-17', null);

    expect($result)->toBe([
        ['date' => '2026-07-17', 'stock_code' => 'BBRI', 'institution' => 70.0, 'retail' => 20.0, 'mixed' => 10.0, 'total_ratio' => 100.0],
    ]);
});

it('forwards optional stock code filter to repository', function () {
    $repository = Mockery::mock(DominanceRatioContract::class);
    $repository->shouldReceive('getDominanceRatio')
        ->once()
        ->with('2026-07-01', '2026-07-17', 'BBRI')
        ->andReturn(collect());
    App::instance(DominanceRatioContract::class, $repository);

    $result = App::make(DominanceRatioService::class)
        ->getDominanceRatio('2026-07-01', '2026-07-17', 'BBRI');

    expect($result)->toBe([]);
});

it('logs and wraps repository failures', function () {
    Log::spy();
    $repository = Mockery::mock(DominanceRatioContract::class);
    $repository->shouldReceive('getDominanceRatio')->once()->andThrow(new RuntimeException('DB down'));
    App::instance(DominanceRatioContract::class, $repository);

    App::make(DominanceRatioService::class)
        ->getDominanceRatio('2026-07-01', '2026-07-17', null);
})->throws(DominanceRatioException::class, 'Failed to get dominance ratio');

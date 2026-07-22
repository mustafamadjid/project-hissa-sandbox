<?php

use App\Features\ForeignFlowVsNetValue\Contracts\ForeignFlowVsNetValueContract;
use App\Features\ForeignFlowVsNetValue\Exceptions\ForeignFlowVsNetValueException;
use App\Features\ForeignFlowVsNetValue\Services\ForeignFlowVsNetValueService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

it('classifies stocks into correct quadrants', function () {
    $repository = Mockery::mock(ForeignFlowVsNetValueContract::class);
    $repository->shouldReceive('getScatterData')
        ->once()
        ->with('2026-07-01', '2026-07-20', null)
        ->andReturn(collect([
            (object) ['stock_code' => 'BBRI', 'foreign_net_flow' => 1000000000, 'net_value' => 2000000000, 'domestic_net_flow' => 500000000],
            (object) ['stock_code' => 'TLKM', 'foreign_net_flow' => -1000000000, 'net_value' => -2000000000, 'domestic_net_flow' => 500000000],
            (object) ['stock_code' => 'ASII', 'foreign_net_flow' => 1000000000, 'net_value' => -2000000000, 'domestic_net_flow' => 500000000],
            (object) ['stock_code' => 'GOTO', 'foreign_net_flow' => -1000000000, 'net_value' => 2000000000, 'domestic_net_flow' => 500000000],
        ]));
    App::instance(ForeignFlowVsNetValueContract::class, $repository);

    $result = App::make(ForeignFlowVsNetValueService::class)
        ->getScatterData('2026-07-01', '2026-07-20', null, null);

    $quadrants = collect($result)->pluck('quadrant', 'stock_code')->all();

    expect($quadrants)->toBe([
        'ASII' => 'foreign_buy_distribution',
        'BBRI' => 'foreign_buy_accumulation',
        'GOTO' => 'foreign_sell_accumulation',
        'TLKM' => 'foreign_sell_distribution',
    ]);
});

it('handles zero values deterministically', function () {
    $repository = Mockery::mock(ForeignFlowVsNetValueContract::class);
    $repository->shouldReceive('getScatterData')
        ->once()
        ->andReturn(collect([
            (object) ['stock_code' => 'ZERO', 'foreign_net_flow' => 0, 'net_value' => 0, 'domestic_net_flow' => 0],
        ]));
    App::instance(ForeignFlowVsNetValueContract::class, $repository);

    $result = App::make(ForeignFlowVsNetValueService::class)
        ->getScatterData('2026-07-01', '2026-07-20', null, null);

    expect($result[0]['quadrant'])->toBe('foreign_buy_accumulation');
});

it('filters by min_abs_value with OR logic', function () {
    $repository = Mockery::mock(ForeignFlowVsNetValueContract::class);
    $repository->shouldReceive('getScatterData')
        ->once()
        ->andReturn(collect([
            (object) ['stock_code' => 'BIG', 'foreign_net_flow' => 5000000000, 'net_value' => 100000000, 'domestic_net_flow' => 0],
            (object) ['stock_code' => 'SMALL', 'foreign_net_flow' => 100000000, 'net_value' => 200000000, 'domestic_net_flow' => 0],
        ]));
    App::instance(ForeignFlowVsNetValueContract::class, $repository);

    $result = App::make(ForeignFlowVsNetValueService::class)
        ->getScatterData('2026-07-01', '2026-07-20', null, 1000000000);

    expect($result)->toHaveCount(1);
    expect($result[0]['stock_code'])->toBe('BIG');
});

it('sorts results deterministically by abs values then stock_code', function () {
    $repository = Mockery::mock(ForeignFlowVsNetValueContract::class);
    $repository->shouldReceive('getScatterData')
        ->once()
        ->andReturn(collect([
            (object) ['stock_code' => 'AA', 'foreign_net_flow' => 3000000000, 'net_value' => 1000000000, 'domestic_net_flow' => 0],
            (object) ['stock_code' => 'BB', 'foreign_net_flow' => 1000000000, 'net_value' => 3000000000, 'domestic_net_flow' => 0],
            (object) ['stock_code' => 'CC', 'foreign_net_flow' => 3000000000, 'net_value' => 1000000000, 'domestic_net_flow' => 0],
        ]));
    App::instance(ForeignFlowVsNetValueContract::class, $repository);

    $result = App::make(ForeignFlowVsNetValueService::class)
        ->getScatterData('2026-07-01', '2026-07-20', null, null);

    expect($result[0]['stock_code'])->toBe('BB');
    expect($result[1]['stock_code'])->toBe('AA');
    expect($result[2]['stock_code'])->toBe('CC');
});

it('returns empty array when no data', function () {
    $repository = Mockery::mock(ForeignFlowVsNetValueContract::class);
    $repository->shouldReceive('getScatterData')
        ->once()
        ->andReturn(collect());
    App::instance(ForeignFlowVsNetValueContract::class, $repository);

    $result = App::make(ForeignFlowVsNetValueService::class)
        ->getScatterData('2026-07-01', '2026-07-20', null, null);

    expect($result)->toBe([]);
});

it('logs and wraps repository failures', function () {
    Log::spy();
    $repository = Mockery::mock(ForeignFlowVsNetValueContract::class);
    $repository->shouldReceive('getScatterData')->once()->andThrow(new RuntimeException('DB down'));
    App::instance(ForeignFlowVsNetValueContract::class, $repository);

    App::make(ForeignFlowVsNetValueService::class)
        ->getScatterData('2026-07-01', '2026-07-20', null, null);
})->throws(ForeignFlowVsNetValueException::class, 'Failed to get foreign flow vs net value data');


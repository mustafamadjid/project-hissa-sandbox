<?php

use App\Features\HeatmapNetValue\Contracts\HeatmapNetValueContract;
use App\Features\HeatmapNetValue\Exceptions\HeatmapNetValueException;
use App\Features\HeatmapNetValue\Services\HeatmapNetValueService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

it('builds dates, stocks, normalized cells, and symmetric color bounds', function () {
    $repository = Mockery::mock(HeatmapNetValueContract::class);
    $repository->shouldReceive('getHeatmapData')
        ->once()
        ->with('2026-07-17', '2026-07-18')
        ->andReturn(collect([
            (object) ['netbs_date' => '2026-07-17', 'stock_code' => 'BBRI', 'net_value' => '2500000000'],
            (object) ['netbs_date' => '2026-07-18', 'stock_code' => 'TLKM', 'net_value' => '-1500000000'],
        ]));
    App::instance(HeatmapNetValueContract::class, $repository);

    $result = App::make(HeatmapNetValueService::class)
        ->getHeatmapData('2026-07-17', '2026-07-18');

    expect($result)->toBe([
        'dates' => ['2026-07-17', '2026-07-18'],
        'stocks' => ['BBRI', 'TLKM'],
        'cells' => [
            ['date' => '2026-07-17', 'stock_code' => 'BBRI', 'net_value' => 2500000000, 'normalized_value' => 1.0],
            ['date' => '2026-07-18', 'stock_code' => 'TLKM', 'net_value' => -1500000000, 'normalized_value' => 0.0],
        ],
        'meta' => ['color_min' => -2500000000, 'color_max' => 2500000000],
    ]);
});

it('returns an empty payload when there is no data for the period', function () {
    $repository = Mockery::mock(HeatmapNetValueContract::class);
    $repository->shouldReceive('getHeatmapData')
        ->once()
        ->with('2026-07-17', '2026-07-18')
        ->andReturn(collect());
    App::instance(HeatmapNetValueContract::class, $repository);

    $result = App::make(HeatmapNetValueService::class)
        ->getHeatmapData('2026-07-17', '2026-07-18');

    expect($result)->toBe([
        'dates' => [],
        'stocks' => [],
        'cells' => [],
        'meta' => ['color_min' => 0, 'color_max' => 0],
    ]);
});

it('logs and wraps repository failures', function () {
    Log::spy();
    $repository = Mockery::mock(HeatmapNetValueContract::class);
    $repository->shouldReceive('getHeatmapData')->once()->andThrow(new RuntimeException('DB down'));
    App::instance(HeatmapNetValueContract::class, $repository);

    App::make(HeatmapNetValueService::class)
        ->getHeatmapData('2026-07-17', '2026-07-18');
})->throws(HeatmapNetValueException::class, 'Failed to get heatmap net value');
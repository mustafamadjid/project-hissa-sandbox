<?php

use App\Features\CumulativeNetValue\Contracts\CumulativeNetValueContract;
use App\Features\CumulativeNetValue\Exceptions\CumulativeNetValueException;
use App\Features\CumulativeNetValue\Services\CumulativeNetValueService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

it('builds running cumulative total from daily values', function () {
    $repository = Mockery::mock(CumulativeNetValueContract::class);
    $repository->shouldReceive('getCumulativeData')
        ->once()
        ->with('BBRI', '2026-07-01', '2026-07-20')
        ->andReturn(collect([
            (object) ['date' => '2026-07-01', 'daily_net_value' => 10000000000],
            (object) ['date' => '2026-07-02', 'daily_net_value' => 20000000000],
            (object) ['date' => '2026-07-03', 'daily_net_value' => -5000000000],
        ]));
    App::instance(CumulativeNetValueContract::class, $repository);

    $result = App::make(CumulativeNetValueService::class)
        ->getCumulativeNetValue('BBRI', '2026-07-01', '2026-07-20');

    expect($result)->toBe([
        ['date' => '2026-07-01', 'daily_net_value' => 10000000000, 'cumulative_net_value' => 10000000000],
        ['date' => '2026-07-02', 'daily_net_value' => 20000000000, 'cumulative_net_value' => 30000000000],
        ['date' => '2026-07-03', 'daily_net_value' => -5000000000, 'cumulative_net_value' => 25000000000],
    ]);
});

it('returns empty array when no data found', function () {
    $repository = Mockery::mock(CumulativeNetValueContract::class);
    $repository->shouldReceive('getCumulativeData')
        ->once()
        ->andReturn(collect());
    App::instance(CumulativeNetValueContract::class, $repository);

    $result = App::make(CumulativeNetValueService::class)
        ->getCumulativeNetValue('BBRI', '2026-07-01', '2026-07-20');

    expect($result)->toBe([]);
});

it('logs and wraps repository failures', function () {
    Log::spy();
    $repository = Mockery::mock(CumulativeNetValueContract::class);
    $repository->shouldReceive('getCumulativeData')->once()->andThrow(new RuntimeException('DB down'));
    App::instance(CumulativeNetValueContract::class, $repository);

    App::make(CumulativeNetValueService::class)
        ->getCumulativeNetValue('BBRI', '2026-07-01', '2026-07-20');
})->throws(CumulativeNetValueException::class, 'Failed to get cumulative net value');

<?php

use App\Features\TopAccumulationDistribution\Contracts\TopAccumulationDistributionContract;
use App\Features\TopAccumulationDistribution\Exceptions\TopAccumulationDistributionException;
use App\Features\TopAccumulationDistribution\Services\TopAccumulationDistributionService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

it('adds separate ranks and classifications for both sides of zero', function () {
    $repository = Mockery::mock(TopAccumulationDistributionContract::class);
    $repository->shouldReceive('getTopAccumulationDistribution')
        ->once()
        ->with('2026-07-01', '2026-07-20', 10)
        ->andReturn([
            'distribution' => collect([(object) ['stock_code' => 'ASII', 'net_value' => '-42000000000']]),
            'accumulation' => collect([(object) ['stock_code' => 'BBRI', 'net_value' => '48000000000']]),
        ]);
    App::instance(TopAccumulationDistributionContract::class, $repository);

    $result = App::make(TopAccumulationDistributionService::class)
        ->getTopAccumulationDistribution('2026-07-01', '2026-07-20', 10);

    expect($result)->toBe([
        ['rank' => 1, 'stock_code' => 'ASII', 'net_value' => -42000000000, 'classification' => 'distribusi'],
        ['rank' => 1, 'stock_code' => 'BBRI', 'net_value' => 48000000000, 'classification' => 'akumulasi'],
    ]);
});

it('logs and wraps repository failures', function () {
    Log::spy();
    $repository = Mockery::mock(TopAccumulationDistributionContract::class);
    $repository->shouldReceive('getTopAccumulationDistribution')->once()->andThrow(new RuntimeException('DB down'));
    App::instance(TopAccumulationDistributionContract::class, $repository);

    App::make(TopAccumulationDistributionService::class)
        ->getTopAccumulationDistribution('2026-07-01', '2026-07-20', 10);
})->throws(TopAccumulationDistributionException::class, 'Failed to get top accumulation distribution');

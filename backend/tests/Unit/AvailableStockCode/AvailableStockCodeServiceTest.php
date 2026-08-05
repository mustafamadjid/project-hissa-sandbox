<?php

use App\Features\AvailableStockCode\Contracts\AvailableStockCodeContract;
use App\Features\AvailableStockCode\Exceptions\AvailableStockCodeException;
use App\Features\AvailableStockCode\Services\AvailableStockCodeService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

it('returns distinct stock codes ordered alphabetically', function () {
    $repository = Mockery::mock(AvailableStockCodeContract::class);
    $repository->shouldReceive('getStockCodes')
        ->once()
        ->andReturn(['BBCA', 'BBRI', 'TLKM']);
    App::instance(AvailableStockCodeContract::class, $repository);

    $result = App::make(AvailableStockCodeService::class)
        ->getStockCodes();

    expect($result)->toBe(['BBCA', 'BBRI', 'TLKM']);
});

it('returns an empty array when there is no data', function () {
    $repository = Mockery::mock(AvailableStockCodeContract::class);
    $repository->shouldReceive('getStockCodes')
        ->once()
        ->andReturn([]);
    App::instance(AvailableStockCodeContract::class, $repository);

    $result = App::make(AvailableStockCodeService::class)
        ->getStockCodes();

    expect($result)->toBe([]);
});

it('logs and wraps repository failures', function () {
    Log::spy();
    $repository = Mockery::mock(AvailableStockCodeContract::class);
    $repository->shouldReceive('getStockCodes')->once()->andThrow(new RuntimeException('DB down'));
    App::instance(AvailableStockCodeContract::class, $repository);

    App::make(AvailableStockCodeService::class)
        ->getStockCodes();
})->throws(AvailableStockCodeException::class, 'Failed to get available stock codes');

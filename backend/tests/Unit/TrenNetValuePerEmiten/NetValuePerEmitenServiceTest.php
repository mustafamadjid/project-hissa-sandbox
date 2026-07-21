<?php

use App\Features\TrenNetValuePerEmiten\Contracts\NetValuePerEmitenContract;
use App\Features\TrenNetValuePerEmiten\Exceptions\NetValuePerEmitenException;
use App\Features\TrenNetValuePerEmiten\Services\NetValuePerEmitenService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

it('delegates getNetValuePerEmiten to repository and returns data', function () {
    $repo = Mockery::mock(NetValuePerEmitenContract::class);
    $expected = collect([
        ['date' => '2024-01-01', 'stock_code' => 'BBCA', 'net_value' => 1000, 'classification' => 'RG'],
        ['date' => '2024-01-02', 'stock_code' => 'BBCA', 'net_value' => 1100, 'classification' => 'RG'],
    ]);

    $repo->shouldReceive('getNetValuePerEmiten')
        ->once()
        ->with('BBCA', '2024-01-01', '2024-01-31')
        ->andReturn($expected);

    App::instance(NetValuePerEmitenContract::class, $repo);

    $service = App::make(NetValuePerEmitenService::class);
    $result = $service->getNetValuePerEmiten('BBCA', '2024-01-01', '2024-01-31');

    expect($result)->toBe($expected);
});

it('logs error and throws NetValuePerEmitenException when repository fails', function () {
    Log::spy();

    $repo = Mockery::mock(NetValuePerEmitenContract::class);
    $repo->shouldReceive('getNetValuePerEmiten')
        ->once()
        ->andThrow(new \RuntimeException('DB down'));

    App::instance(NetValuePerEmitenContract::class, $repo);

    $service = App::make(NetValuePerEmitenService::class);

    try {
        $service->getNetValuePerEmiten('BBCA', '2024-01-01', '2024-01-31');
        $this->fail('Expected exception not thrown');
    } catch (NetValuePerEmitenException $e) {
        Log::shouldHaveReceived('error')->once();
        expect($e->getMessage())->toBe('Failed to get net value per emiten');
    }
});

it('preserves previous exception in thrown NetValuePerEmitenException', function () {
    $previous = new \RuntimeException('Connection timeout');

    $repo = Mockery::mock(NetValuePerEmitenContract::class);
    $repo->shouldReceive('getNetValuePerEmiten')
        ->once()
        ->andThrow($previous);

    App::instance(NetValuePerEmitenContract::class, $repo);

    $service = App::make(NetValuePerEmitenService::class);

    try {
        $service->getNetValuePerEmiten('BBCA', '2024-01-01', '2024-01-31');
        $this->fail('Expected exception not thrown');
    } catch (NetValuePerEmitenException $e) {
        expect($e->getPrevious())->toBe($previous);
    }
});
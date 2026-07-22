<?php

use App\Features\ForeignBuyVsSell\Contracts\ForeignBuyVsSellContract;
use App\Features\ForeignBuyVsSell\Exceptions\ForeignBuyVsSellException;
use App\Features\ForeignBuyVsSell\Services\ForeignBuyVsSellService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

it('maps repository rows into gross flow points', function () {
    $repository = Mockery::mock(ForeignBuyVsSellContract::class);
    $repository->shouldReceive('getGrossFlow')
        ->once()
        ->with('BBRI', '2026-07-01', '2026-07-17')
        ->andReturn(collect([
            (object) [
                'date' => '2026-07-17',
                'foreign_buy' => '5000000000',
                'foreign_sell' => '3000000000',
                'foreign_net_flow' => '2000000000',
            ],
        ]));
    App::instance(ForeignBuyVsSellContract::class, $repository);

    $result = App::make(ForeignBuyVsSellService::class)
        ->getGrossFlow('BBRI', '2026-07-01', '2026-07-17');

    expect($result)->toBe([
        [
            'date' => '2026-07-17',
            'foreign_buy' => 5000000000,
            'foreign_sell' => 3000000000,
            'foreign_net_flow' => 2000000000,
        ],
    ]);
});

it('logs and wraps repository failures', function () {
    Log::spy();
    $repository = Mockery::mock(ForeignBuyVsSellContract::class);
    $repository->shouldReceive('getGrossFlow')->once()->andThrow(new RuntimeException('DB down'));
    App::instance(ForeignBuyVsSellContract::class, $repository);

    App::make(ForeignBuyVsSellService::class)
        ->getGrossFlow('BBRI', '2026-07-01', '2026-07-17');
})->throws(ForeignBuyVsSellException::class, 'Failed to get foreign gross flow');
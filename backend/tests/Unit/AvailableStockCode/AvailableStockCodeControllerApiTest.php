<?php

use App\Features\AvailableStockCode\Contracts\AvailableStockCodeContract;

it('returns stock codes list', function () {
    $this->mock(AvailableStockCodeContract::class, function ($mock) {
        $mock->shouldReceive('getStockCodes')
            ->once()
            ->andReturn(['BBCA', 'BBRI', 'TLKM']);
    });

    $this->getJson('/api/v1/market/stock-codes')
        ->assertOk()
        ->assertJson([
            'items' => ['BBCA', 'BBRI', 'TLKM'],
        ]);
});

it('returns an empty items array when there is no data', function () {
    $this->mock(AvailableStockCodeContract::class, function ($mock) {
        $mock->shouldReceive('getStockCodes')
            ->once()
            ->andReturn([]);
    });

    $this->getJson('/api/v1/market/stock-codes')
        ->assertOk()
        ->assertJson([
            'items' => [],
        ]);
});

it('returns 500 on service failure', function () {
    $this->mock(AvailableStockCodeContract::class, function ($mock) {
        $mock->shouldReceive('getStockCodes')
            ->once()
            ->andThrow(new RuntimeException('DB down'));
    });

    $this->getJson('/api/v1/market/stock-codes')
        ->assertInternalServerError()
        ->assertJsonPath('success', false);
});

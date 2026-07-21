<?php

use App\Features\TopAccumulationDistribution\Contracts\TopAccumulationDistributionContract;

it('returns ranked accumulation and distribution response', function () {
    $this->mock(TopAccumulationDistributionContract::class, function ($mock) {
        $mock->shouldReceive('getTopAccumulationDistribution')
            ->once()
            ->with('2026-07-01', '2026-07-20', 10)
            ->andReturn([
                'distribution' => collect([(object) ['stock_code' => 'ASII', 'net_value' => -42000000000]]),
                'accumulation' => collect([(object) ['stock_code' => 'BBRI', 'net_value' => 48000000000]]),
            ]);
    });

    $this->getJson('/api/v1/market/net-value-ranking?start_date=2026-07-01&end_date=2026-07-20')
        ->assertOk()
        ->assertJson([
            'period' => ['start_date' => '2026-07-01', 'end_date' => '2026-07-20'],
            'items' => [
                ['rank' => 1, 'stock_code' => 'ASII', 'net_value' => -42000000000, 'classification' => 'distribusi'],
                ['rank' => 1, 'stock_code' => 'BBRI', 'net_value' => 48000000000, 'classification' => 'akumulasi'],
            ],
            'meta' => ['limit' => 10, 'aggregation' => 'sum', 'unit' => 'IDR'],
        ]);
});

it('passes validated query params and limit to service', function () {
    $this->mock(TopAccumulationDistributionContract::class, function ($mock) {
        $mock->shouldReceive('getTopAccumulationDistribution')
            ->once()
            ->with('2026-07-01', '2026-07-20', 5)
            ->andReturn(['distribution' => collect(), 'accumulation' => collect()]);
    });

    $this->getJson('/api/v1/market/net-value-ranking?start_date=2026-07-01&end_date=2026-07-20&limit=5')
        ->assertOk()
        ->assertJsonPath('meta.limit', 5);
});

it('rejects missing or invalid query dates', function () {
    $this->getJson('/api/v1/market/net-value-ranking?start_date=01-07-2026&end_date=2026-07-20')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['start_date']);

    $this->getJson('/api/v1/market/net-value-ranking?start_date=2026-07-20&end_date=2026-07-01')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['end_date']);
});
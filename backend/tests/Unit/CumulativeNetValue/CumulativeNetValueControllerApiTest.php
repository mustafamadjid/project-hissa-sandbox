<?php

use App\Features\CumulativeNetValue\Contracts\CumulativeNetValueContract;

it('returns cumulative net value response with required keys', function () {
    $this->mock(CumulativeNetValueContract::class, function ($mock) {
        $mock->shouldReceive('getCumulativeData')
            ->once()
            ->with('BBRI', '2026-07-01', '2026-07-20')
            ->andReturn(collect([
                (object) ['date' => '2026-07-01', 'daily_net_value' => 25000000000],
            ]));
    });

    $this->getJson('/api/v1/market/stocks/BBRI/cumulative-net-value?start_date=2026-07-01&end_date=2026-07-20')
        ->assertOk()
        ->assertJson([
            'stock_code' => 'BBRI',
            'period' => ['start_date' => '2026-07-01', 'end_date' => '2026-07-20'],
            'points' => [
                ['date' => '2026-07-01', 'daily_net_value' => 25000000000, 'cumulative_net_value' => 25000000000],
            ],
            'meta' => [
                'reset_policy' => 'start_of_period',
                'unit' => 'IDR',
                'granularity' => 'daily',
            ],
        ]);
});

it('normalizes stock_code to uppercase', function () {
    $this->mock(CumulativeNetValueContract::class, function ($mock) {
        $mock->shouldReceive('getCumulativeData')
            ->once()
            ->with('BBRI', '2026-07-01', '2026-07-20')
            ->andReturn(collect());
    });

    $this->getJson('/api/v1/market/stocks/bbri/cumulative-net-value?start_date=2026-07-01&end_date=2026-07-20')
        ->assertOk()
        ->assertJsonPath('stock_code', 'BBRI');
});

it('rejects missing or invalid dates', function () {
    $this->getJson('/api/v1/market/stocks/BBRI/cumulative-net-value?start_date=01-07-2026&end_date=2026-07-20')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['start_date']);

    $this->getJson('/api/v1/market/stocks/BBRI/cumulative-net-value?start_date=2026-07-20&end_date=2026-07-01')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['end_date']);
});

it('rejects invalid stock_code', function () {
    $this->getJson('/api/v1/market/stocks/$INVALID/cumulative-net-value?start_date=2026-07-01&end_date=2026-07-20')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['stock_code']);
});

it('rejects invalid reset value', function () {
    $this->getJson('/api/v1/market/stocks/BBRI/cumulative-net-value?start_date=2026-07-01&end_date=2026-07-20&reset=every_day')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['reset']);
});

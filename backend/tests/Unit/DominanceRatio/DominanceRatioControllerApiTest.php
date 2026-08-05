<?php

use App\Features\DominanceRatio\Contracts\DominanceRatioContract;

it('returns dominance ratio timeline response', function () {
    $this->mock(DominanceRatioContract::class, function ($mock) {
        $mock->shouldReceive('getDominanceRatio')
            ->once()
            ->with('BBRI', '2026-07-01', '2026-07-17', 'daily')
            ->andReturn(collect([
                (object) ['date' => '2026-07-17', 'stock_code' => 'BBRI', 'institution' => '70.00', 'retail' => '20.00', 'mixed' => '10.00'],
            ]));
    });

    $this->getJson('/api/v1/market/dominance-ratio?start_date=2026-07-01&end_date=2026-07-17&stock_code=bbri')
        ->assertOk()
        ->assertJson([
            'stock_code' => 'BBRI',
            'period' => ['start_date' => '2026-07-01', 'end_date' => '2026-07-17'],
            'granularity' => 'daily',
            'points' => [
                ['date' => '2026-07-17', 'institution_ratio' => 70.0, 'retail_ratio' => 20.0, 'mixed_ratio' => 10.0, 'total_ratio' => 100.0],
            ],
            'meta' => ['ratio_basis' => 'transaction_value', 'unit' => 'percent', 'aggregation' => 'daily', 'timezone' => 'Asia/Jakarta'],
        ]);
});

it('passes timeline filters to service', function () {
    $this->mock(DominanceRatioContract::class, function ($mock) {
        $mock->shouldReceive('getDominanceRatio')
            ->once()
            ->with('BBRI', '2026-07-01', '2026-07-17', 'monthly')
            ->andReturn(collect());
    });

    $this->getJson('/api/v1/market/dominance-ratio?start_date=2026-07-01&end_date=2026-07-17&stock_code=BBRI&granularity=monthly')
        ->assertOk()
        ->assertJsonPath('points', [])
        ->assertJsonPath('meta.aggregation', 'latest');
});

it('rejects missing or invalid query dates', function () {
    $this->getJson('/api/v1/market/dominance-ratio?start_date=01-07-2026&end_date=2026-07-17')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['start_date']);

    $this->getJson('/api/v1/market/dominance-ratio?start_date=2026-07-17&end_date=2026-07-01')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['end_date']);
});

it('rejects missing stock code and invalid granularity', function () {
    $this->getJson('/api/v1/market/dominance-ratio?start_date=2026-07-01&end_date=2026-07-17')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['stock_code']);

    $this->getJson('/api/v1/market/dominance-ratio?start_date=2026-07-01&end_date=2026-07-17&stock_code=BBRI&granularity=yearly')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['granularity']);
});

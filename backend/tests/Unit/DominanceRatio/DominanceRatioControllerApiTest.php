<?php

use App\Features\DominanceRatio\Contracts\DominanceRatioContract;

it('returns dominance ratio items response', function () {
    $this->mock(DominanceRatioContract::class, function ($mock) {
        $mock->shouldReceive('getDominanceRatio')
            ->once()
            ->with('2026-07-01', '2026-07-17', null)
            ->andReturn(collect([
                (object) ['date' => '2026-07-17', 'stock_code' => 'BBRI', 'institution' => '70.00', 'retail' => '20.00', 'mixed' => '10.00'],
            ]));
    });

    $this->getJson('/api/v1/market/dominance-ratio?start_date=2026-07-01&end_date=2026-07-17')
        ->assertOk()
        ->assertJson([
            'items' => [
                ['date' => '2026-07-17', 'stock_code' => 'BBRI', 'institution' => 70.0, 'retail' => 20.0, 'mixed' => 10.0, 'total_ratio' => 100.0],
            ],
            'meta' => ['ratio_basis' => 'transaction_value', 'unit' => 'percent'],
        ]);
});

it('passes optional stock code filter to service', function () {
    $this->mock(DominanceRatioContract::class, function ($mock) {
        $mock->shouldReceive('getDominanceRatio')
            ->once()
            ->with('2026-07-01', '2026-07-17', 'BBRI')
            ->andReturn(collect());
    });

    $this->getJson('/api/v1/market/dominance-ratio?start_date=2026-07-01&end_date=2026-07-17&stock_code=BBRI')
        ->assertOk()
        ->assertJsonPath('items', []);
});

it('rejects missing or invalid query dates', function () {
    $this->getJson('/api/v1/market/dominance-ratio?start_date=01-07-2026&end_date=2026-07-17')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['start_date']);

    $this->getJson('/api/v1/market/dominance-ratio?start_date=2026-07-17&end_date=2026-07-01')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['end_date']);
});

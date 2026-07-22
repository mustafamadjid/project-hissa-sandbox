<?php

use App\Features\ForeignFlowVsNetValue\Contracts\ForeignFlowVsNetValueContract;

it('returns scatter response with required keys', function () {
    $this->mock(ForeignFlowVsNetValueContract::class, function ($mock) {
        $mock->shouldReceive('getScatterData')
            ->once()
            ->with('2026-07-01', '2026-07-20', null)
            ->andReturn(collect([
                (object) [
                    'stock_code' => 'BBRI',
                    'foreign_net_flow' => 20000000000,
                    'net_value' => 25000000000,
                    'domestic_net_flow' => 5000000000,
                ],
            ]));
    });

    $this->getJson('/api/v1/market/foreign-flow-net-value-scatter?start_date=2026-07-01&end_date=2026-07-20')
        ->assertOk()
        ->assertJson([
            'period' => ['start_date' => '2026-07-01', 'end_date' => '2026-07-20'],
            'items' => [
                [
                    'stock_code' => 'BBRI',
                    'foreign_net_flow' => 20000000000,
                    'net_value' => 25000000000,
                    'domestic_net_flow' => 5000000000,
                    'quadrant' => 'foreign_buy_accumulation',
                ],
            ],
            'meta' => [
                'unit' => 'IDR',
                'aggregation' => 'sum',
            ],
        ]);
});

it('parses and normalizes stock_codes', function () {
    $this->mock(ForeignFlowVsNetValueContract::class, function ($mock) {
        $mock->shouldReceive('getScatterData')
            ->once()
            ->with('2026-07-01', '2026-07-20', ['BBRI', 'TLKM'])
            ->andReturn(collect());
    });

    $this->getJson('/api/v1/market/foreign-flow-net-value-scatter?start_date=2026-07-01&end_date=2026-07-20&stock_codes=bbri,tlkm')
        ->assertOk()
        ->assertJson(['items' => []]);
});

it('rejects missing or invalid dates', function () {
    $this->getJson('/api/v1/market/foreign-flow-net-value-scatter?start_date=01-07-2026&end_date=2026-07-20')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['start_date']);

    $this->getJson('/api/v1/market/foreign-flow-net-value-scatter?start_date=2026-07-20&end_date=2026-07-01')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['end_date']);
});

it('rejects invalid aggregation', function () {
    $this->getJson('/api/v1/market/foreign-flow-net-value-scatter?start_date=2026-07-01&end_date=2026-07-20&aggregation=avg')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['aggregation']);
});

it('rejects invalid stock_codes format', function () {
    $this->getJson('/api/v1/market/foreign-flow-net-value-scatter?start_date=2026-07-01&end_date=2026-07-20&stock_codes=BBRI;TLKM')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['stock_codes']);
});

it('rejects negative min_abs_value', function () {
    $this->getJson('/api/v1/market/foreign-flow-net-value-scatter?start_date=2026-07-01&end_date=2026-07-20&min_abs_value=-1000000000')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['min_abs_value']);
});

it('rejects invalid limit', function () {
    $this->getJson('/api/v1/market/foreign-flow-net-value-scatter?start_date=2026-07-01&end_date=2026-07-20&limit=0')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['limit']);
});

it('returns empty items array when no data', function () {
    $this->mock(ForeignFlowVsNetValueContract::class, function ($mock) {
        $mock->shouldReceive('getScatterData')
            ->once()
            ->andReturn(collect());
    });

    $this->getJson('/api/v1/market/foreign-flow-net-value-scatter?start_date=2026-07-01&end_date=2026-07-20')
        ->assertOk()
        ->assertJson(['items' => []]);
});

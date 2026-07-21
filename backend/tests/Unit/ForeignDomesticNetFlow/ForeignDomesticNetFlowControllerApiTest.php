<?php

use App\Features\ForeignDomesticNetFlow\Contracts\ForeignDomesticNetFlowContract;

it('returns foreign vs domestic net flow response', function () {
    $this->mock(ForeignDomesticNetFlowContract::class, function ($mock) {
        $mock->shouldReceive('getNetFlow')
            ->once()
            ->with('BBRI', '2026-07-01', '2026-07-17')
            ->andReturn(collect([
                (object) ['date' => '2026-07-17', 'foreign_net_flow' => '2000000000', 'domestic_net_flow' => '500000000'],
            ]));
    });

    $this->getJson('/api/v1/market/stocks/BBRI/investor/net-flow?start_date=2026-07-01&end_date=2026-07-17')
        ->assertOk()
        ->assertJson([
            'stock_code' => 'BBRI',
            'points' => [
                ['date' => '2026-07-17', 'foreign_net_flow' => 2000000000, 'domestic_net_flow' => 500000000],
            ],
            'meta' => ['unit' => 'IDR', 'granularity' => 'daily'],
        ]);
});

it('accepts granularity daily', function () {
    $this->mock(ForeignDomesticNetFlowContract::class, function ($mock) {
        $mock->shouldReceive('getNetFlow')
            ->once()
            ->with('BBRI', '2026-07-01', '2026-07-17')
            ->andReturn(collect([
                (object) ['date' => '2026-07-17', 'foreign_net_flow' => '2000000000', 'domestic_net_flow' => '500000000'],
            ]));
    });

    $this->getJson('/api/v1/market/stocks/BBRI/investor/net-flow?start_date=2026-07-01&end_date=2026-07-17&granularity=daily')
        ->assertOk()
        ->assertJsonPath('meta.granularity', 'daily');
});

it('rejects invalid granularity', function () {
    $this->getJson('/api/v1/market/stocks/BBRI/investor/net-flow?start_date=2026-07-01&end_date=2026-07-17&granularity=hourly')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['granularity']);
});

it('rejects invalid query dates', function () {
    $this->getJson('/api/v1/market/stocks/BBRI/investor/net-flow?start_date=01-07-2026&end_date=2026-07-17')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['start_date']);

    $this->getJson('/api/v1/market/stocks/BBRI/investor/net-flow?start_date=2026-07-17&end_date=2026-07-01')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['end_date']);
});

it('rejects missing query params', function () {
    $this->getJson('/api/v1/market/stocks/BBRI/investor/net-flow')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['start_date', 'end_date']);
});

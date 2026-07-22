<?php

use App\Features\HeatmapNetValue\Contracts\HeatmapNetValueContract;

it('returns the heatmap payload with dates, stocks, cells, and meta', function () {
    $this->mock(HeatmapNetValueContract::class, function ($mock) {
        $mock->shouldReceive('getHeatmapData')
            ->once()
            ->with('2026-07-17', '2026-07-18')
            ->andReturn(collect([
                (object) ['netbs_date' => '2026-07-17', 'stock_code' => 'BBRI', 'net_value' => '2500000000'],
                (object) ['netbs_date' => '2026-07-18', 'stock_code' => 'TLKM', 'net_value' => '-1500000000'],
            ]));
    });

    $this->getJson('/api/v1/market/net-value-heatmap?start_date=2026-07-17&end_date=2026-07-18')
        ->assertOk()
        ->assertJson([
            'dates' => ['2026-07-17', '2026-07-18'],
            'stocks' => ['BBRI', 'TLKM'],
            'cells' => [
                ['date' => '2026-07-17', 'stock_code' => 'BBRI', 'net_value' => 2500000000, 'normalized_value' => 1.0],
                ['date' => '2026-07-18', 'stock_code' => 'TLKM', 'net_value' => -1500000000, 'normalized_value' => 0.0],
            ],
            'meta' => ['color_min' => -2500000000, 'color_max' => 2500000000],
        ]);
});

it('returns an empty heatmap payload when there is no data', function () {
    $this->mock(HeatmapNetValueContract::class, function ($mock) {
        $mock->shouldReceive('getHeatmapData')
            ->once()
            ->with('2026-07-17', '2026-07-18')
            ->andReturn(collect());
    });

    $this->getJson('/api/v1/market/net-value-heatmap?start_date=2026-07-17&end_date=2026-07-18')
        ->assertOk()
        ->assertJson([
            'dates' => [],
            'stocks' => [],
            'cells' => [],
            'meta' => ['color_min' => 0, 'color_max' => 0],
        ]);
});

it('rejects missing or invalid query dates', function () {
    $this->getJson('/api/v1/market/net-value-heatmap?start_date=17-07-2026&end_date=2026-07-18')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['start_date']);

    $this->getJson('/api/v1/market/net-value-heatmap?start_date=2026-07-18&end_date=2026-07-17')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['end_date']);
});
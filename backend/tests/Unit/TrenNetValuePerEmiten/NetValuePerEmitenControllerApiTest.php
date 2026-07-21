<?php

use App\Features\TrenNetValuePerEmiten\Contracts\NetValuePerEmitenContract;

it('returns JSON payload on success', function () {
    $data = collect([
        ['date' => '2024-01-01', 'stock_code' => 'BBCA', 'net_value' => 1000, 'classification' => 'RG'],
        ['date' => '2024-01-02', 'stock_code' => 'BBCA', 'net_value' => 1100, 'classification' => 'RG'],
    ]);

    $this->mock(NetValuePerEmitenContract::class, function ($mock) use ($data) {
        $mock->shouldReceive('getNetValuePerEmiten')->once()
            ->with('BBCA', '2024-01-01', '2024-01-31')->andReturn($data);
    });

    $this->getJson('/api/v1/tren-net-value/BBCA?start_date=2024-01-01&end_date=2024-01-31')
        ->assertOk()
        ->assertJsonStructure([
            'stock_code',
            'period' => ['start_date', 'end_date'],
            'points',
            'meta' => ['unit'],
        ])
        ->assertJson([
            'stock_code' => 'BBCA',
            'period' => ['start_date' => '2024-01-01', 'end_date' => '2024-01-31'],
            'meta' => ['unit' => 'IDR'],
            'points' => $data->toArray(),
        ]);
});

it('returns empty points when no data', function () {
    $this->mock(NetValuePerEmitenContract::class, function ($mock) {
        $mock->shouldReceive('getNetValuePerEmiten')->once()->andReturn(collect());
    });

    $this->getJson('/api/v1/tren-net-value/UNKN?start_date=2024-01-01&end_date=2024-01-31')
        ->assertOk()->assertJsonPath('points', []);
});

it('returns 500 JSON when repository fails', function () {
    $this->mock(NetValuePerEmitenContract::class, function ($mock) {
        $mock->shouldReceive('getNetValuePerEmiten')->once()
            ->andThrow(new RuntimeException('DB down'));
    });

    $this->getJson('/api/v1/tren-net-value/BBCA?start_date=2024-01-01&end_date=2024-01-31')
        ->assertStatus(500)
        ->assertJson(['message' => 'Failed to get net value per emiten']);
});

it('passes empty strings when query params missing', function () {
    $this->mock(NetValuePerEmitenContract::class, function ($mock) {
        $mock->shouldReceive('getNetValuePerEmiten')
            ->once()
            ->with('BBCA', '', '')
            ->andReturn(collect());
    });

    $this->getJson('/api/v1/tren-net-value/BBCA')
        ->assertOk()
        ->assertJsonPath('period', ['start_date' => null, 'end_date' => null]);
});

it('returns 429 when rate limit exceeded', function () {
    $this->mock(NetValuePerEmitenContract::class, function ($mock) {
        $mock->shouldReceive('getNetValuePerEmiten')->times(10)->andReturn(collect());
    });

    for ($i = 0; $i < 10; $i++) {
        $this->getJson('/api/v1/tren-net-value/BBCA?start_date=2024-01-01&end_date=2024-01-31')->assertOk();
    }

    $this->getJson('/api/v1/tren-net-value/BBCA?start_date=2024-01-01&end_date=2024-01-31')
        ->assertStatus(429);
});

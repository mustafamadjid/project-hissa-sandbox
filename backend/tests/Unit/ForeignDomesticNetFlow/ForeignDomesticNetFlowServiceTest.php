<?php

use App\Features\ForeignDomesticNetFlow\Contracts\ForeignDomesticNetFlowContract;
use App\Features\ForeignDomesticNetFlow\Exceptions\ForeignDomesticNetFlowException;
use App\Features\ForeignDomesticNetFlow\Services\ForeignDomesticNetFlowService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

it('maps repository rows into net flow points', function () {
    $repository = Mockery::mock(ForeignDomesticNetFlowContract::class);
    $repository->shouldReceive('getNetFlow')
        ->once()
        ->with('BBRI', '2026-07-01', '2026-07-17')
        ->andReturn(collect([
            (object) ['date' => '2026-07-17', 'foreign_net_flow' => '2000000000', 'domestic_net_flow' => '500000000'],
        ]));
    App::instance(ForeignDomesticNetFlowContract::class, $repository);

    $result = App::make(ForeignDomesticNetFlowService::class)
        ->getNetFlow('BBRI', '2026-07-01', '2026-07-17');

    expect($result)->toBe([
        ['date' => '2026-07-17', 'foreign_net_flow' => 2000000000, 'domestic_net_flow' => 500000000],
    ]);
});

it('logs and wraps repository failures', function () {
    Log::spy();
    $repository = Mockery::mock(ForeignDomesticNetFlowContract::class);
    $repository->shouldReceive('getNetFlow')->once()->andThrow(new RuntimeException('DB down'));
    App::instance(ForeignDomesticNetFlowContract::class, $repository);

    App::make(ForeignDomesticNetFlowService::class)
        ->getNetFlow('BBRI', '2026-07-01', '2026-07-17');
})->throws(ForeignDomesticNetFlowException::class, 'Failed to get foreign vs domestic net flow');

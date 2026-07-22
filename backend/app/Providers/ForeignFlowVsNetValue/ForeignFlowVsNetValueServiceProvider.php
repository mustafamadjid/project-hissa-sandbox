<?php

namespace App\Providers\ForeignFlowVsNetValue;

use App\Features\ForeignFlowVsNetValue\Contracts\ForeignFlowVsNetValueContract;
use App\Features\ForeignFlowVsNetValue\Repositories\EloquentForeignFlowVsNetValue;
use Illuminate\Support\ServiceProvider;

final class ForeignFlowVsNetValueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ForeignFlowVsNetValueContract::class,
            EloquentForeignFlowVsNetValue::class,
        );
    }
}

<?php

namespace App\Providers\HeatmapNetValue;

use App\Features\HeatmapNetValue\Contracts\HeatmapNetValueContract;
use App\Features\HeatmapNetValue\Repositories\EloquentHeatmapNetValue;
use Illuminate\Support\ServiceProvider;

final class HeatmapNetValueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            HeatmapNetValueContract::class,
            EloquentHeatmapNetValue::class,
        );
    }
}
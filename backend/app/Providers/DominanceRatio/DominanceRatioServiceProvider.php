<?php

namespace App\Providers\DominanceRatio;

use App\Features\DominanceRatio\Contracts\DominanceRatioContract;
use App\Features\DominanceRatio\Repositories\EloquentDominanceRatio;
use Illuminate\Support\ServiceProvider;

final class DominanceRatioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            DominanceRatioContract::class,
            EloquentDominanceRatio::class,
        );
    }
}

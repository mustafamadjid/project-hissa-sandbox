<?php

namespace App\Providers\CumulativeNetValue;

use App\Features\CumulativeNetValue\Contracts\CumulativeNetValueContract;
use App\Features\CumulativeNetValue\Repositories\EloquentCumulativeNetValue;
use Illuminate\Support\ServiceProvider;

final class CumulativeNetValueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CumulativeNetValueContract::class,
            EloquentCumulativeNetValue::class,
        );
    }
}

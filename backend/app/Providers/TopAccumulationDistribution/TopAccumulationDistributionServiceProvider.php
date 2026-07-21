<?php

namespace App\Providers\TopAccumulationDistribution;

use App\Features\TopAccumulationDistribution\Contracts\TopAccumulationDistributionContract;
use App\Features\TopAccumulationDistribution\Repositories\EloquentTopAccumulationDistribution;
use Illuminate\Support\ServiceProvider;

final class TopAccumulationDistributionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            TopAccumulationDistributionContract::class,
            EloquentTopAccumulationDistribution::class,
        );
    }
}

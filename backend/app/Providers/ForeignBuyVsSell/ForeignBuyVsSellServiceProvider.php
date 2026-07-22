<?php

namespace App\Providers\ForeignBuyVsSell;

use App\Features\ForeignBuyVsSell\Contracts\ForeignBuyVsSellContract;
use App\Features\ForeignBuyVsSell\Repositories\EloquentForeignBuyVsSell;
use Illuminate\Support\ServiceProvider;

final class ForeignBuyVsSellServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ForeignBuyVsSellContract::class,
            EloquentForeignBuyVsSell::class,
        );
    }
}
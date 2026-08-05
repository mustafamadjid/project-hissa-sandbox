<?php

namespace App\Providers\AvailableStockCode;

use App\Features\AvailableStockCode\Contracts\AvailableStockCodeContract;
use App\Features\AvailableStockCode\Repositories\EloquentAvailableStockCode;
use Illuminate\Support\ServiceProvider;

final class AvailableStockCodeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AvailableStockCodeContract::class,
            EloquentAvailableStockCode::class,
        );
    }
}

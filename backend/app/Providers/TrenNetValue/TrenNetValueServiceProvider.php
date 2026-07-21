<?php

namespace App\Providers\TrenNetValue;

use App\Features\TrenNetValuePerEmiten\Contracts\NetValuePerEmitenContract;
use App\Features\TrenNetValuePerEmiten\Repositories\EloquentNetValuePerEmiten;
use Illuminate\Support\ServiceProvider;

class TrenNetValueServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NetValuePerEmitenContract::class, EloquentNetValuePerEmiten::class);
    }

    public function boot(): void
    {
    }
}

<?php

namespace App\Providers\ForeignDomesticNetFlow;

use App\Features\ForeignDomesticNetFlow\Contracts\ForeignDomesticNetFlowContract;
use App\Features\ForeignDomesticNetFlow\Repositories\EloquentForeignDomesticNetFlow;
use Illuminate\Support\ServiceProvider;

final class ForeignDomesticNetFlowServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            ForeignDomesticNetFlowContract::class,
            EloquentForeignDomesticNetFlow::class,
        );
    }
}

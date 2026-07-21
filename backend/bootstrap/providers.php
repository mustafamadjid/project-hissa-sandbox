<?php

use App\Providers\AppServiceProvider;
use App\Providers\TopAccumulationDistribution\TopAccumulationDistributionServiceProvider;
use App\Providers\TrenNetValue\TrenNetValueServiceProvider;

return [
    AppServiceProvider::class,
    TopAccumulationDistributionServiceProvider::class,
    TrenNetValueServiceProvider::class,
];

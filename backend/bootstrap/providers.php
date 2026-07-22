<?php

use App\Providers\AppServiceProvider;
use App\Providers\DominanceRatio\DominanceRatioServiceProvider;
use App\Providers\HeatmapNetValue\HeatmapNetValueServiceProvider;
use App\Providers\TopAccumulationDistribution\TopAccumulationDistributionServiceProvider;
use App\Providers\TrenNetValue\TrenNetValueServiceProvider;
use App\Providers\ForeignDomesticNetFlow\ForeignDomesticNetFlowServiceProvider;

return [
    AppServiceProvider::class,
    TopAccumulationDistributionServiceProvider::class,
    TrenNetValueServiceProvider::class,
    ForeignDomesticNetFlowServiceProvider::class,
    DominanceRatioServiceProvider::class,
    HeatmapNetValueServiceProvider::class,
];
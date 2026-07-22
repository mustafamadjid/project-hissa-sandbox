<?php

use App\Providers\AppServiceProvider;
use App\Providers\CumulativeNetValue\CumulativeNetValueServiceProvider;
use App\Providers\DominanceRatio\DominanceRatioServiceProvider;
use App\Providers\ForeignBuyVsSell\ForeignBuyVsSellServiceProvider;
use App\Providers\ForeignDomesticNetFlow\ForeignDomesticNetFlowServiceProvider;
use App\Providers\ForeignFlowVsNetValue\ForeignFlowVsNetValueServiceProvider;
use App\Providers\HeatmapNetValue\HeatmapNetValueServiceProvider;
use App\Providers\TopAccumulationDistribution\TopAccumulationDistributionServiceProvider;
use App\Providers\TrenNetValue\TrenNetValueServiceProvider;

return [
    AppServiceProvider::class,
    TopAccumulationDistributionServiceProvider::class,
    TrenNetValueServiceProvider::class,
    ForeignDomesticNetFlowServiceProvider::class,
    ForeignBuyVsSellServiceProvider::class,
    CumulativeNetValueServiceProvider::class,
    ForeignFlowVsNetValueServiceProvider::class,
    DominanceRatioServiceProvider::class,
    HeatmapNetValueServiceProvider::class,
];

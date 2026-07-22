<?php

use App\Features\DominanceRatio\Http\Controller\DominanceRatioController;
use App\Features\ForeignBuyVsSell\Http\Controller\ForeignBuyVsSellController;
use App\Features\ForeignDomesticNetFlow\Http\Controller\ForeignDomesticNetFlowController;
use App\Features\HeatmapNetValue\Http\Controller\HeatmapNetValueController;
use App\Features\TopAccumulationDistribution\Http\Controller\TopAccumulationDistributionController;
use App\Features\CumulativeNetValue\Http\Controller\CumulativeNetValueController;
use App\Features\ForeignFlowVsNetValue\Http\Controller\ForeignFlowVsNetValueController;
use App\Features\TrenNetValuePerEmiten\Http\Controller\NetValuePerEmitenController;
use Illuminate\Support\Facades\Route;

Route::prefix('')->name('api.v1.')->middleware('throttle:ip')->group(function () {
    Route::get('/tren-net-value/{stockCode}', NetValuePerEmitenController::class);
    Route::get('/market/stocks/{stock_code}/cumulative-net-value', CumulativeNetValueController::class);
    Route::get('/market/foreign-flow-net-value-scatter', ForeignFlowVsNetValueController::class);
    Route::get('/market/net-value-heatmap', HeatmapNetValueController::class);
    Route::get('/market/net-value-ranking', TopAccumulationDistributionController::class);
    Route::get('/market/stocks/{stock_code}/investor/net-flow', ForeignDomesticNetFlowController::class);
    Route::get('/market/stocks/{stock_code}/foreign/gross-flow', ForeignBuyVsSellController::class);
    Route::get('/market/dominance-ratio', DominanceRatioController::class);
});

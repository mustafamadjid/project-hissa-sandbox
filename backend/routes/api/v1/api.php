<?php

use App\Features\DominanceRatio\Http\Controller\DominanceRatioController;
use App\Features\ForeignDomesticNetFlow\Http\Controller\ForeignDomesticNetFlowController;
use App\Features\TopAccumulationDistribution\Http\Controller\TopAccumulationDistributionController;
use App\Features\TrenNetValuePerEmiten\Http\Controller\NetValuePerEmitenController;
use Illuminate\Support\Facades\Route;

Route::prefix('')->name('api.v1.')->middleware('throttle:ip')->group(function () {
    Route::get('/tren-net-value/{stockCode}', NetValuePerEmitenController::class);
    Route::get('/market/net-value-ranking', TopAccumulationDistributionController::class);
    Route::get('/market/stocks/{stock_code}/investor/net-flow', ForeignDomesticNetFlowController::class);
    Route::get('/market/dominance-ratio', DominanceRatioController::class);
});

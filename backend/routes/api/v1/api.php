<?php

use App\Features\TopAccumulationDistribution\Http\Controller\TopAccumulationDistributionController;
use App\Features\TrenNetValuePerEmiten\Http\Controller\NetValuePerEmitenController;
use Illuminate\Support\Facades\Route;


Route::prefix('api/v1')->name('api.v1.')->middlewaare('throttle:ip')->group(function () {
    Route::get('/tren-net-value/{stockCode}', NetValuePerEmitenController::class);
    Route::get('/market/net-value-ranking', TopAccumulationDistributionController::class);
});
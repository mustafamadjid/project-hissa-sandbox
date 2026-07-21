<?php

use App\Features\TrenNetValuePerEmiten\Http\Controller\NetValuePerEmitenController;
use Illuminate\Support\Facades\Route;

Route::get('/tren-net-value/{stockCode}', NetValuePerEmitenController::class)->middleware('throttle:ip');

<?php

namespace App\Features\ForeignBuyVsSell\Contracts;

use Illuminate\Support\Collection;

interface ForeignBuyVsSellContract
{
    public function getGrossFlow(string $stockCode, string $startDate, string $endDate): Collection;
}
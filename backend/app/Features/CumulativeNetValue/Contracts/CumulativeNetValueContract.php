<?php

namespace App\Features\CumulativeNetValue\Contracts;

use Illuminate\Support\Collection;

interface CumulativeNetValueContract
{
    public function getCumulativeData(string $stockCode, string $startDate, string $endDate): Collection;
}

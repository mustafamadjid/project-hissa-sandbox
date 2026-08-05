<?php

namespace App\Features\DominanceRatio\Contracts;

use Illuminate\Support\Collection;

interface DominanceRatioContract
{
    public function getDominanceRatio(string $stockCode, string $startDate, string $endDate, string $granularity): Collection;
}

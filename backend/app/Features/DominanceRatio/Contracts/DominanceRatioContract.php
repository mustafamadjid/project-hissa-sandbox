<?php

namespace App\Features\DominanceRatio\Contracts;

use Illuminate\Support\Collection;

interface DominanceRatioContract
{
    public function getDominanceRatio(string $startDate, string $endDate, ?string $stockCode): Collection;
}

<?php

namespace App\Features\HeatmapNetValue\Contracts;

use Illuminate\Support\Collection;

interface HeatmapNetValueContract
{
    public function getHeatmapData(string $startDate, string $endDate): Collection;
}
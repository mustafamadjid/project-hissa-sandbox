<?php

namespace App\Features\HeatmapNetValue\Contracts;

use Illuminate\Support\Collection;

interface HeatmapNetValueContract
{
    public function getHeatmapData(string $startDate, string $endDate): Collection;

    public function getDistinctDates(string $startDate, string $endDate): array;

    public function getDistinctStocks(string $startDate, string $endDate): array;
}
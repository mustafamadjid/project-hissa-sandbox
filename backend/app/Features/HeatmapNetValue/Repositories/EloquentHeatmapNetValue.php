<?php

namespace App\Features\HeatmapNetValue\Repositories;

use App\Features\HeatmapNetValue\Contracts\HeatmapNetValueContract;
use App\Features\HeatmapNetValue\Models\HeatmapNetValue;
use Illuminate\Support\Collection;
use Override;

final class EloquentHeatmapNetValue implements HeatmapNetValueContract
{
    #[Override]
    public function getHeatmapData(string $startDate, string $endDate): Collection
    {
        return HeatmapNetValue::query()
            ->select('netbs_date')
            ->selectRaw('netbs_stock_code as stock_code')
            ->selectRaw('netval as net_value')
            ->whereBetween('netbs_date', [$startDate, $endDate])
            ->orderBy('netbs_date')
            ->orderBy('netbs_stock_code')
            ->get();
    }
}
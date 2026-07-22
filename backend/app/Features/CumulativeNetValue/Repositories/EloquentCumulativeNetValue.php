<?php

namespace App\Features\CumulativeNetValue\Repositories;

use App\Features\CumulativeNetValue\Contracts\CumulativeNetValueContract;
use App\Features\CumulativeNetValue\Models\CumulativeNetValue;
use Illuminate\Support\Collection;
use Override;

final class EloquentCumulativeNetValue implements CumulativeNetValueContract
{
    #[Override]
    public function getCumulativeData(string $stockCode, string $startDate, string $endDate): Collection
    {
        return CumulativeNetValue::query()
            ->selectRaw('DATE(netbs_date) as date')
            ->selectRaw('SUM(netval) as daily_net_value')
            ->where('netbs_stock_code', $stockCode)
            ->whereBetween('netbs_date', [$startDate, $endDate])
            ->groupByRaw('DATE(netbs_date)')
            ->orderByRaw('DATE(netbs_date)')
            ->get();
    }
}

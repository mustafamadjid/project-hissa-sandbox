<?php

namespace App\Features\ForeignBuyVsSell\Repositories;

use App\Features\ForeignBuyVsSell\Contracts\ForeignBuyVsSellContract;
use App\Features\ForeignBuyVsSell\Models\ForeignBuyVsSell;
use App\Support\Observability\PerformanceTracker;
use Illuminate\Support\Collection;
use Override;

final class EloquentForeignBuyVsSell implements ForeignBuyVsSellContract
{
    #[Override]
    public function getGrossFlow(string $stockCode, string $startDate, string $endDate): Collection
    {
        return PerformanceTracker::measure(
            'EloquentForeignBuyVsSell@getGrossFlow',
            fn (): Collection => ForeignBuyVsSell::query()
                ->selectRaw('
                    netbs_date AS date,
                    SUM(Foreign_buy) AS foreign_buy,
                    SUM(Foreign_sell) AS foreign_sell,
                    SUM(foreign_net_flow) AS foreign_net_flow
                ')
                ->where('netbs_stock_code', $stockCode)
                ->whereBetween('netbs_date', [$startDate, $endDate])
                ->groupBy('netbs_date')
                ->orderBy('netbs_date')
                ->get(),
        );
    }
}

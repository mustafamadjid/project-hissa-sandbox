<?php

namespace App\Features\ForeignBuyVsSell\Repositories;

use App\Features\ForeignBuyVsSell\Contracts\ForeignBuyVsSellContract;
use App\Features\ForeignBuyVsSell\Models\ForeignBuyVsSell;
use Illuminate\Support\Collection;
use Override;

final class EloquentForeignBuyVsSell implements ForeignBuyVsSellContract
{
    #[Override]
    public function getGrossFlow(string $stockCode, string $startDate, string $endDate): Collection
    {
        return ForeignBuyVsSell::query()
            ->where('netbs_stock_code', $stockCode)
            ->whereBetween('netbs_date', [$startDate, $endDate])
            ->orderBy('netbs_date')
            ->selectRaw("DATE(netbs_date) as date, SUM(Foreign_buy) as foreign_buy, SUM(Foreign_sell) as foreign_sell, SUM(foreign_net_flow) as foreign_net_flow")
            ->groupByRaw("DATE(netbs_date)")
            ->get();
    }
}
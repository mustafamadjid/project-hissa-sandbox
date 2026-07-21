<?php

namespace App\Features\ForeignDomesticNetFlow\Repositories;

use App\Features\ForeignDomesticNetFlow\Contracts\ForeignDomesticNetFlowContract;
use App\Features\ForeignDomesticNetFlow\Models\ForeignDomesticNetFlow;
use Illuminate\Support\Collection;
use Override;

final class EloquentForeignDomesticNetFlow implements ForeignDomesticNetFlowContract
{
    #[Override]
    public function getNetFlow(string $stockCode, string $startDate, string $endDate): Collection
    {
        return ForeignDomesticNetFlow::query()
            ->where('netbs_stock_code', $stockCode)
            ->whereBetween('netbs_date', [$startDate, $endDate])
            ->orderBy('netbs_date')
            ->selectRaw("DATE(netbs_date) as date, SUM(Foreign_net_flow) as foreign_net_flow, SUM(Domestic_net_flow) as domestic_net_flow")
            ->groupByRaw("DATE(netbs_date)")
            ->get();
    }
}

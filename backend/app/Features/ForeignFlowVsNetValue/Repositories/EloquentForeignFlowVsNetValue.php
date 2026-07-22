<?php

namespace App\Features\ForeignFlowVsNetValue\Repositories;

use App\Features\ForeignFlowVsNetValue\Contracts\ForeignFlowVsNetValueContract;
use App\Features\ForeignFlowVsNetValue\Models\ForeignFlow;
use App\Features\ForeignFlowVsNetValue\Models\NetValue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Override;

final class EloquentForeignFlowVsNetValue implements ForeignFlowVsNetValueContract
{
    #[Override]
    public function getScatterData(string $startDate, string $endDate, ?array $stockCodes): Collection
    {
        $foreignFlowQuery = ForeignFlow::query()
            ->select('netbs_stock_code')
            ->selectRaw('SUM(Foreign_net_flow) as foreign_net_flow')
            ->selectRaw('SUM(Domestic_net_flow) as domestic_net_flow')
            ->whereBetween('netbs_date', [$startDate, $endDate])
            ->groupBy('netbs_stock_code');

        $netValueQuery = NetValue::query()
            ->select('netbs_stock_code')
            ->selectRaw('SUM(netval) as net_value')
            ->whereBetween('netbs_date', [$startDate, $endDate])
            ->groupBy('netbs_stock_code');

        if ($stockCodes !== null) {
            $foreignFlowQuery->whereIn('netbs_stock_code', $stockCodes);
            $netValueQuery->whereIn('netbs_stock_code', $stockCodes);
        }

        return DB::query()
            ->fromSub($foreignFlowQuery, 'ff')
            ->joinSub($netValueQuery, 'nv', 'ff.netbs_stock_code', '=', 'nv.netbs_stock_code')
            ->selectRaw('ff.netbs_stock_code as stock_code')
            ->selectRaw('ff.foreign_net_flow')
            ->selectRaw('ff.domestic_net_flow')
            ->selectRaw('nv.net_value')
            ->get();
    }
}

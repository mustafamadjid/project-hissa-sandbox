<?php

namespace App\Features\DominanceRatio\Repositories;

use App\Features\DominanceRatio\Contracts\DominanceRatioContract;
use App\Features\DominanceRatio\Models\DominanceRatio;
use Illuminate\Support\Collection;
use Override;

final class EloquentDominanceRatio implements DominanceRatioContract
{
    #[Override]
    public function getDominanceRatio(string $startDate, string $endDate, ?string $stockCode): Collection
    {
        return DominanceRatio::query()
            ->select([
                'netbs_date',
                'netbs_stock_code as stock_code',
                'netval as net_value',
            ])
            ->whereBetween('netbs_date', [$startDate, $endDate])
            ->orderBy('netbs_date')
            ->orderBy('netbs_stock_code')
            ->get();
    }
}

<?php

namespace App\Features\DominanceRatio\Repositories;

use App\Features\DominanceRatio\Contracts\DominanceRatioContract;
use App\Features\DominanceRatio\Models\DominanceRatio;
use App\Support\Observability\PerformanceTracker;
use Illuminate\Support\Collection;
use Override;

final class EloquentDominanceRatio implements DominanceRatioContract
{
    #[Override]
    public function getDominanceRatio(string $startDate, string $endDate, ?string $stockCode): Collection
    {
        return PerformanceTracker::measure(
            'EloquentDominanceRatio@getDominanceRatio',
            fn (): Collection => DominanceRatio::query()
                ->select([
                    'netbs_date as date',
                    'netbs_stock_code as stock_code',
                    'Institusi as institution',
                    'Retail as retail',
                    'Mixed as mixed',
                ])
                ->whereBetween('netbs_date', [$startDate, $endDate])
                ->orderBy('netbs_date')
                ->orderBy('stock_code')
                ->get(),
        );
    }
}

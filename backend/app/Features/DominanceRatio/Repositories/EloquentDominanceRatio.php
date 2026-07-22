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
            ->whereBetween('netbs_date', [$startDate, $endDate])
            ->when($stockCode, fn ($query) => $query->where('netbs_stock_code', $stockCode))
            ->orderBy('netbs_date', 'desc')
            ->orderBy('netbs_stock_code', 'asc')
            ->get([
                'netbs_date as date',
                'netbs_stock_code as stock_code',
                'Institusi as institution',
                'Retail as retail',
                'Mixed as mixed',
            ]);
    }
}

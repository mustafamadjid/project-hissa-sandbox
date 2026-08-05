<?php

namespace App\Features\AvailableStockCode\Repositories;

use App\Features\AvailableStockCode\Contracts\AvailableStockCodeContract;
use App\Features\AvailableStockCode\Models\AvailableStockCode;
use App\Support\Observability\PerformanceTracker;
use Override;

final class EloquentAvailableStockCode implements AvailableStockCodeContract
{
    #[Override]
    public function getStockCodes(): array
    {
        return PerformanceTracker::measure(
            'EloquentAvailableStockCode@getStockCodes',
            fn (): array => AvailableStockCode::query()
                ->whereNotNull('netbs_stock_code')
                ->distinct()
                ->orderBy('netbs_stock_code')
                ->pluck('netbs_stock_code')
                ->toArray(),
        );
    }
}

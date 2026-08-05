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
    public function getDominanceRatio(string $stockCode, string $startDate, string $endDate, string $granularity): Collection
    {
        return PerformanceTracker::measure(
            'EloquentDominanceRatio@getDominanceRatio',
            fn (): Collection => $this->query($stockCode, $startDate, $endDate, $granularity)->get(),
        );
    }

    private function query(string $stockCode, string $startDate, string $endDate, string $granularity)
    {
        $query = DominanceRatio::query()
            ->select([
                'netbs_date as date',
                'Institusi as institution',
                'Retail as retail',
                'Mixed as mixed',
            ])
            ->where('netbs_stock_code', $stockCode)
            ->whereBetween('netbs_date', [$startDate, $endDate])
            ->orderBy('netbs_date');

        if ($granularity === 'weekly') {
            return $query->whereRaw('netbs_date = (SELECT MAX(candidate.netbs_date) FROM dominance_ratio AS candidate WHERE candidate.netbs_stock_code = dominance_ratio.netbs_stock_code AND candidate.netbs_date BETWEEN ? AND ? AND YEARWEEK(candidate.netbs_date, 3) = YEARWEEK(dominance_ratio.netbs_date, 3))', [$startDate, $endDate]);
        }

        if ($granularity === 'monthly') {
            return $query->whereRaw('netbs_date = (SELECT MAX(candidate.netbs_date) FROM dominance_ratio AS candidate WHERE candidate.netbs_stock_code = dominance_ratio.netbs_stock_code AND candidate.netbs_date BETWEEN ? AND ? AND YEAR(candidate.netbs_date) = YEAR(dominance_ratio.netbs_date) AND MONTH(candidate.netbs_date) = MONTH(dominance_ratio.netbs_date))', [$startDate, $endDate]);
        }

        return $query;
    }
}

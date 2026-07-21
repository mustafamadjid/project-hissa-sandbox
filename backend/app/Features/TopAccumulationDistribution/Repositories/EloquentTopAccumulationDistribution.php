<?php

namespace App\Features\TopAccumulationDistribution\Repositories;

use App\Features\TopAccumulationDistribution\Contracts\TopAccumulationDistributionContract;
use App\Features\TopAccumulationDistribution\Models\TopAccumulationDistribution;
use Illuminate\Support\Collection;
use Override;

final class EloquentTopAccumulationDistribution implements TopAccumulationDistributionContract
{
    #[Override]
    public function getTopAccumulationDistribution(string $startDate, string $endDate, int $limit): array
    {
        return [
            'distribution' => $this->rankedStocks($startDate, $endDate, '<', 'asc', $limit),
            'accumulation' => $this->rankedStocks($startDate, $endDate, '>', 'desc', $limit),
        ];
    }

    private function rankedStocks(
        string $startDate,
        string $endDate,
        string $operator,
        string $direction,
        int $limit,
    ): Collection {
        return TopAccumulationDistribution::query()
            ->select('netbs_stock_code as stock_code')
            ->selectRaw('SUM(netval) as net_value')
            ->whereBetween('netbs_date', [$startDate, $endDate])
            ->groupBy('netbs_stock_code')
            ->havingRaw("SUM(netval) {$operator} 0")
            ->orderBy('net_value', $direction)
            ->limit($limit)
            ->get();
    }
}

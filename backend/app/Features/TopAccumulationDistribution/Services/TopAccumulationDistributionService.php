<?php

namespace App\Features\TopAccumulationDistribution\Services;

use App\Features\TopAccumulationDistribution\Contracts\TopAccumulationDistributionContract;
use App\Features\TopAccumulationDistribution\Exceptions\TopAccumulationDistributionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class TopAccumulationDistributionService
{
    public function __construct(
        private readonly TopAccumulationDistributionContract $repository,
    ) {}

    public function getTopAccumulationDistribution(string $startDate, string $endDate, int $limit): array
    {
        try {
            $rankedStocks = $this->repository->getTopAccumulationDistribution($startDate, $endDate, $limit);

            return [
                ...$this->withRanks($rankedStocks['distribution'], 'distribusi'),
                ...$this->withRanks($rankedStocks['accumulation'], 'akumulasi'),
            ];
        } catch (\Throwable $exception) {
            Log::error('Failed to get top accumulation distribution', ['exception' => $exception]);

            throw new TopAccumulationDistributionException(
                'Failed to get top accumulation distribution',
                0,
                $exception,
            );
        }
    }

    private function withRanks(Collection $stocks, string $classification): array
    {
        return $stocks
            ->values()
            ->map(fn ($stock, int $index) => [
                'rank' => $index + 1,
                'stock_code' => $stock->stock_code,
                'net_value' => (int) $stock->net_value,
                'classification' => $classification,
            ])
            ->all();
    }
}

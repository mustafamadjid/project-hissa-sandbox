<?php

namespace App\Features\DominanceRatio\Services;

use App\Features\DominanceRatio\Contracts\DominanceRatioContract;
use App\Features\DominanceRatio\Exceptions\DominanceRatioException;
use App\Support\Observability\PerformanceTracker;
use Illuminate\Support\Facades\Log;

final class DominanceRatioService
{
    public function __construct(
        private readonly DominanceRatioContract $repository,
    ) {}

    public function getDominanceRatio(string $stockCode, string $startDate, string $endDate, string $granularity): array
    {
        return PerformanceTracker::measure(
            'DominanceRatioService@getDominanceRatio',
            function () use ($stockCode, $startDate, $endDate, $granularity): array {
                try {
                    $points = $this->repository
                        ->getDominanceRatio($stockCode, $startDate, $endDate, $granularity)
                        ->map(fn ($item) => [
                            'date' => $item->date,
                            'institution_ratio' => (float) $item->institution,
                            'retail_ratio' => (float) $item->retail,
                            'mixed_ratio' => (float) $item->mixed,
                            'total_ratio' => (float) $item->institution + (float) $item->retail + (float) $item->mixed,
                        ])
                        ->all();

                    $invalidRatioCount = collect($points)
                        ->filter(fn (array $point) => $point['total_ratio'] < 99.99 || $point['total_ratio'] > 100.01)
                        ->count();

                    if ($invalidRatioCount > 0) {
                        Log::warning('Dominance ratio total outside tolerance', [
                            'stock_code' => $stockCode,
                            'start_date' => $startDate,
                            'end_date' => $endDate,
                            'granularity' => $granularity,
                            'invalid_ratio_count' => $invalidRatioCount,
                        ]);
                    }

                    return [
                        'stock_code' => $stockCode,
                        'period' => ['start_date' => $startDate, 'end_date' => $endDate],
                        'granularity' => $granularity,
                        'points' => $points,
                        'meta' => [
                            'unit' => 'percent',
                            'ratio_basis' => 'transaction_value',
                            'aggregation' => $granularity === 'daily' ? 'daily' : 'latest',
                            'timezone' => 'Asia/Jakarta',
                        ],
                    ];
                } catch (\Throwable $exception) {
                    Log::error('Failed to get dominance ratio', ['exception' => $exception]);

                    throw new DominanceRatioException(
                        'Failed to get dominance ratio',
                        0,
                        $exception,
                    );
                }
            },
        );
    }
}

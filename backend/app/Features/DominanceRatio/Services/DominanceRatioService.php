<?php

namespace App\Features\DominanceRatio\Services;

use App\Features\DominanceRatio\Contracts\DominanceRatioContract;
use App\Features\DominanceRatio\Exceptions\DominanceRatioException;
use Illuminate\Support\Facades\Log;

final class DominanceRatioService
{
    public function __construct(
        private readonly DominanceRatioContract $repository,
    ) {}

    public function getDominanceRatio(string $startDate, string $endDate, ?string $stockCode): array
    {
        try {
            return $this->repository
                ->getDominanceRatio($startDate, $endDate, $stockCode)
                ->map(fn ($item) => [
                    'date' => $item->date,
                    'stock_code' => $item->stock_code,
                    'institution' => (float) $item->institution,
                    'retail' => (float) $item->retail,
                    'mixed' => (float) $item->mixed,
                    'total_ratio' => (float) $item->institution + (float) $item->retail + (float) $item->mixed,
                ])
                ->all();
        } catch (\Throwable $exception) {
            Log::error('Failed to get dominance ratio', ['exception' => $exception]);

            throw new DominanceRatioException(
                'Failed to get dominance ratio',
                0,
                $exception,
            );
        }
    }
}

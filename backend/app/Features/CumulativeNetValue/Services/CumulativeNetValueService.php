<?php

namespace App\Features\CumulativeNetValue\Services;

use App\Features\CumulativeNetValue\Contracts\CumulativeNetValueContract;
use App\Features\CumulativeNetValue\Exceptions\CumulativeNetValueException;
use Illuminate\Support\Facades\Log;

final class CumulativeNetValueService
{
    public function __construct(
        private readonly CumulativeNetValueContract $repository,
    ) {}

    public function getCumulativeNetValue(string $stockCode, string $startDate, string $endDate): array
    {
        try {
            $cumulativeNetValue = 0;

            return $this->repository
                ->getCumulativeData($stockCode, $startDate, $endDate)
                ->map(function ($row) use (&$cumulativeNetValue): array {
                    $dailyNetValue = (int) $row->daily_net_value;
                    $cumulativeNetValue += $dailyNetValue;

                    return [
                        'date' => $this->dateString($row->date),
                        'daily_net_value' => $dailyNetValue,
                        'cumulative_net_value' => $cumulativeNetValue,
                    ];
                })
                ->all();
        } catch (\Throwable $exception) {
            Log::error('Failed to get cumulative net value', ['exception' => $exception]);

            throw new CumulativeNetValueException(
                'Failed to get cumulative net value',
                0,
                $exception,
            );
        }
    }

    private function dateString(mixed $date): string
    {
        return $date instanceof \DateTimeInterface ? $date->format('Y-m-d') : (string) $date;
    }
}

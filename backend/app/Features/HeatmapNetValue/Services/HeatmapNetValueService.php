<?php

namespace App\Features\HeatmapNetValue\Services;

use App\Features\HeatmapNetValue\Contracts\HeatmapNetValueContract;
use App\Features\HeatmapNetValue\Exceptions\HeatmapNetValueException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

final class HeatmapNetValueService
{
    public function __construct(
        private readonly HeatmapNetValueContract $repository,
    ) {}

    public function getHeatmapData(string $startDate, string $endDate): array
    {
        try {
            $rows = $this->repository->getHeatmapData($startDate, $endDate);

            if ($rows->isEmpty()) {
                return [
                    'dates' => [],
                    'stocks' => [],
                    'cells' => [],
                    'meta' => [
                        'color_min' => 0,
                        'color_max' => 0,
                    ],
                ];
            }

            $netValues = $rows->map(fn ($row) => (float) $row->net_value);
            $min = $netValues->min();
            $max = $netValues->max();
            $range = $max - $min;

            $dates = $rows
                ->map(fn ($row) => $this->dateString($row->netbs_date))
                ->unique()
                ->sort()
                ->values()
                ->all();

            $stocks = $rows
                ->map(fn ($row) => $row->stock_code)
                ->unique()
                ->sort()
                ->values()
                ->all();

            $cells = $rows
                ->map(fn ($row) => [
                    'date' => $this->dateString($row->netbs_date),
                    'stock_code' => $row->stock_code,
                    'net_value' => (int) $row->net_value,
                    'normalized_value' => $range > 0
                        ? round(($row->net_value - $min) / $range, 4)
                        : 0.5,
                ])
                ->all();

            $maxAbs = max(abs($min), abs($max));

            return [
                'dates' => $dates,
                'stocks' => $stocks,
                'cells' => $cells,
                'meta' => [
                    'color_min' => (int) -$maxAbs,
                    'color_max' => (int) $maxAbs,
                ],
            ];
        } catch (\Throwable $exception) {
            Log::error('Failed to get heatmap net value', ['exception' => $exception]);

            throw new HeatmapNetValueException(
                'Failed to get heatmap net value',
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
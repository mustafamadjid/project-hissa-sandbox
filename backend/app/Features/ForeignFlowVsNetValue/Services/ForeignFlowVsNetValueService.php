<?php

namespace App\Features\ForeignFlowVsNetValue\Services;

use App\Features\ForeignFlowVsNetValue\Contracts\ForeignFlowVsNetValueContract;
use App\Features\ForeignFlowVsNetValue\Exceptions\ForeignFlowVsNetValueException;
use Illuminate\Support\Facades\Log;

final class ForeignFlowVsNetValueService
{
    public function __construct(
        private readonly ForeignFlowVsNetValueContract $repository,
    ) {}

    public function getScatterData(string $startDate, string $endDate, ?array $stockCodes, ?float $minAbsValue): array
    {
        try {
            $items = $this->repository
                ->getScatterData($startDate, $endDate, $stockCodes)
                ->map(fn ($row) => [
                    'stock_code' => $row->stock_code,
                    'foreign_net_flow' => (int) $row->foreign_net_flow,
                    'net_value' => (int) $row->net_value,
                    'domestic_net_flow' => (int) $row->domestic_net_flow,
                    'quadrant' => $this->quadrant((int) $row->foreign_net_flow, (int) $row->net_value),
                ])
                ->filter(fn (array $item) => $minAbsValue === null
                    || abs($item['foreign_net_flow']) >= $minAbsValue
                    || abs($item['net_value']) >= $minAbsValue)
                ->values()
                ->all();

            usort($items, function (array $first, array $second): int {
                return (abs($second['net_value']) <=> abs($first['net_value']))
                    ?: (abs($second['foreign_net_flow']) <=> abs($first['foreign_net_flow']))
                    ?: ($first['stock_code'] <=> $second['stock_code']);
            });

            return $items;
        } catch (\Throwable $exception) {
            Log::error('Failed to get foreign flow vs net value data', ['exception' => $exception]);

            throw new ForeignFlowVsNetValueException(
                'Failed to get foreign flow vs net value data',
                0,
                $exception,
            );
        }
    }

    private function quadrant(int $foreignNetFlow, int $netValue): string
    {
        if ($foreignNetFlow >= 0 && $netValue >= 0) {
            return 'foreign_buy_accumulation';
        }

        if ($foreignNetFlow < 0 && $netValue < 0) {
            return 'foreign_sell_distribution';
        }

        if ($foreignNetFlow >= 0) {
            return 'foreign_buy_distribution';
        }

        return 'foreign_sell_accumulation';
    }
}

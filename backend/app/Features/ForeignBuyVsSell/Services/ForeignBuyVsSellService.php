<?php

namespace App\Features\ForeignBuyVsSell\Services;

use App\Features\ForeignBuyVsSell\Contracts\ForeignBuyVsSellContract;
use App\Features\ForeignBuyVsSell\Exceptions\ForeignBuyVsSellException;
use Illuminate\Support\Facades\Log;

final class ForeignBuyVsSellService
{
    public function __construct(
        private readonly ForeignBuyVsSellContract $repository,
    ) {}

    public function getGrossFlow(string $stockCode, string $startDate, string $endDate): array
    {
        try {
            $data = $this->repository->getGrossFlow($stockCode, $startDate, $endDate);

            return $data->map(fn ($item) => [
                'date' => $item->date,
                'foreign_buy' => (int) $item->foreign_buy,
                'foreign_sell' => (int) $item->foreign_sell,
                'foreign_net_flow' => (int) $item->foreign_net_flow,
            ])->all();
        } catch (\Throwable $exception) {
            Log::error('Failed to get foreign gross flow', ['exception' => $exception]);

            throw new ForeignBuyVsSellException(
                'Failed to get foreign gross flow',
                0,
                $exception,
            );
        }
    }
}
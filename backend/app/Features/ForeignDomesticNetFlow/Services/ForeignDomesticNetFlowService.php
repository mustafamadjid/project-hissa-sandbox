<?php

namespace App\Features\ForeignDomesticNetFlow\Services;

use App\Features\ForeignDomesticNetFlow\Contracts\ForeignDomesticNetFlowContract;
use App\Features\ForeignDomesticNetFlow\Exceptions\ForeignDomesticNetFlowException;
use Illuminate\Support\Facades\Log;

final class ForeignDomesticNetFlowService
{
    public function __construct(
        private readonly ForeignDomesticNetFlowContract $repository,
    ) {}

    public function getNetFlow(string $stockCode, string $startDate, string $endDate): array
    {
        try {
            $data = $this->repository->getNetFlow($stockCode, $startDate, $endDate);

            return $data->map(fn ($item) => [
                'date' => $item->date,
                'foreign_net_flow' => (int) $item->foreign_net_flow,
                'domestic_net_flow' => (int) $item->domestic_net_flow,
            ])->all();
        } catch (\Throwable $exception) {
            Log::error('Failed to get foreign vs domestic net flow', ['exception' => $exception]);

            throw new ForeignDomesticNetFlowException(
                'Failed to get foreign vs domestic net flow',
                0,
                $exception,
            );
        }
    }
}


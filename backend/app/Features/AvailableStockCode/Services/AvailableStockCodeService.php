<?php

namespace App\Features\AvailableStockCode\Services;

use App\Features\AvailableStockCode\Contracts\AvailableStockCodeContract;
use App\Features\AvailableStockCode\Exceptions\AvailableStockCodeException;
use App\Support\Observability\PerformanceTracker;
use Illuminate\Support\Facades\Log;

final class AvailableStockCodeService
{
    public function __construct(
        private readonly AvailableStockCodeContract $repository,
    ) {}

    public function getStockCodes(): array
    {
        return PerformanceTracker::measure(
            'AvailableStockCodeService@getStockCodes',
            function (): array {
                try {
                    return $this->repository->getStockCodes();
                } catch (\Throwable $exception) {
                    Log::error('Failed to get available stock codes', ['exception' => $exception]);

                    throw new AvailableStockCodeException(
                        'Failed to get available stock codes',
                        0,
                        $exception,
                    );
                }
            },
        );
    }
}

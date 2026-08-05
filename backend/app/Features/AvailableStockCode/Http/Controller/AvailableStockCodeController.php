<?php

namespace App\Features\AvailableStockCode\Http\Controller;

use App\Features\AvailableStockCode\Exceptions\AvailableStockCodeException;
use App\Features\AvailableStockCode\Services\AvailableStockCodeService;
use App\Http\Controllers\Controller;
use App\Support\Observability\PerformanceTracker;
use Illuminate\Http\JsonResponse;

final class AvailableStockCodeController extends Controller
{
    public function __construct(
        private readonly AvailableStockCodeService $service,
    ) {}

    public function __invoke(): JsonResponse
    {
        try {
            return PerformanceTracker::measure(
                'AvailableStockCodeController@__invoke',
                fn (): JsonResponse => response()->json([
                    'items' => $this->service->getStockCodes(),
                ]),
            );
        } catch (AvailableStockCodeException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return $this->serverError('Terjadi kesalahan tak terduga.');
        }
    }
}

<?php

namespace App\Features\DominanceRatio\Http\Controller;

use App\Features\DominanceRatio\Exceptions\DominanceRatioException;
use App\Features\DominanceRatio\Services\DominanceRatioService;
use App\Http\Controllers\Controller;
use App\Support\Observability\PerformanceTracker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class DominanceRatioController extends Controller
{
    public function __construct(
        private readonly DominanceRatioService $service,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            if (is_string($request->query('stock_code'))) {
                $request->query->set('stock_code', trim($request->query('stock_code')));
            }

            $validator = Validator::make($request->query(), [
                'start_date' => ['required', 'date_format:Y-m-d'],
                'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
                'stock_code' => ['required', 'string', 'regex:/^[A-Za-z0-9]{1,20}$/'],
                'granularity' => ['sometimes', 'string', 'in:daily,weekly,monthly'],
            ]);

            $validated = $validator->validate();
            $stockCode = strtoupper($validated['stock_code']);
            $granularity = $validated['granularity'] ?? 'daily';

            return PerformanceTracker::measure(
                'DominanceRatioController@__invoke',
                function () use ($validated, $stockCode, $granularity): JsonResponse {
                    $response = $this->service->getDominanceRatio(
                        $stockCode,
                        $validated['start_date'],
                        $validated['end_date'],
                        $granularity,
                    );

                    return response()->json($response);
                },
            );
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (DominanceRatioException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return $this->serverError('Terjadi kesalahan tak terduga.');
        }
    }
}

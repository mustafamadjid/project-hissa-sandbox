<?php

namespace App\Features\CumulativeNetValue\Http\Controller;

use App\Features\CumulativeNetValue\Exceptions\CumulativeNetValueException;
use App\Features\CumulativeNetValue\Services\CumulativeNetValueService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class CumulativeNetValueController extends Controller
{
    public function __construct(
        private readonly CumulativeNetValueService $service,
    ) {}

    public function __invoke(Request $request, string $stock_code): JsonResponse
    {
        try {
            $validator = Validator::make(
                array_merge($request->query(), ['stock_code' => $stock_code]),
                [
                    'stock_code' => ['required', 'string', 'max:10', 'regex:/^[A-Za-z0-9]{1,10}$/'],
                    'start_date' => ['required', 'date_format:Y-m-d'],
                    'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
                    'reset' => ['nullable', 'string', 'in:start_of_period'],
                ]
            );

            $validated = $validator->validate();
            $stockCode = strtoupper($validated['stock_code']);
            $cumulativeNetValue = $this->service->getCumulativeNetValue(
                $stockCode,
                $validated['start_date'],
                $validated['end_date'],
            );

            return response()->json([
                'stock_code' => $stockCode,
                'period' => [
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                ],
                'points' => $cumulativeNetValue,
                'meta' => [
                    'reset_policy' => $validated['reset'] ?? 'start_of_period',
                    'unit' => 'IDR',
                    'granularity' => 'daily',
                ],
            ]);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (CumulativeNetValueException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return $this->serverError('Terjadi kesalahan tak terduga.');
        }
    }
}

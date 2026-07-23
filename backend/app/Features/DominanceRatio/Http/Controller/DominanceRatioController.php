<?php

namespace App\Features\DominanceRatio\Http\Controller;

use App\Features\DominanceRatio\Exceptions\DominanceRatioException;
use App\Features\DominanceRatio\Services\DominanceRatioService;
use App\Http\Controllers\Controller;
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
            $validator = Validator::make($request->query(), [
                'start_date' => ['required', 'date_format:Y-m-d'],
                'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
                'stock_code' => ['nullable', 'string', 'max:10'],
            ]);

            $validated = $validator->validate();
            $stockCode = isset($validated['stock_code'])
                ? strtoupper($validated['stock_code'])
                : null;
            $items = $this->service->getDominanceRatio(
                $validated['start_date'],
                $validated['end_date'],
                $stockCode,
            );

            return response()->json([
                'items' => $items,
                'meta' => [
                    'ratio_basis' => 'transaction_value',
                    'unit' => 'percent',
                ],
            ]);
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

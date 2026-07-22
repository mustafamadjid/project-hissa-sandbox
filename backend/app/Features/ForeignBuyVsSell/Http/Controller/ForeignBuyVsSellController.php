<?php

namespace App\Features\ForeignBuyVsSell\Http\Controller;

use App\Features\ForeignBuyVsSell\Exceptions\ForeignBuyVsSellException;
use App\Features\ForeignBuyVsSell\Services\ForeignBuyVsSellService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class ForeignBuyVsSellController extends Controller
{
    public function __construct(
        private readonly ForeignBuyVsSellService $service,
    ) {}

    public function __invoke(Request $request, string $stock_code): JsonResponse
    {
        try {
            $validator = Validator::make(
                array_merge($request->query(), ['stock_code' => $stock_code]),
                [
                    'stock_code' => ['required', 'string', 'max:10'],
                    'start_date' => ['required', 'date_format:Y-m-d'],
                    'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
                    'granularity' => ['nullable', 'string', 'in:daily'],
                ]
            );

            $validated = $validator->validate();

            return response()->json([
                'stock_code' => $validated['stock_code'],
                'points' => $this->service->getGrossFlow(
                    $validated['stock_code'],
                    $validated['start_date'],
                    $validated['end_date'],
                ),
                'meta' => [
                    'unit' => 'IDR',
                    'granularity' => $validated['granularity'] ?? 'daily',
                ],
            ]);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (ForeignBuyVsSellException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return $this->serverError('Terjadi kesalahan tak terduga.');
        }
    }
}
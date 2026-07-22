<?php

namespace App\Features\ForeignDomesticNetFlow\Http\Controller;

use App\Features\ForeignDomesticNetFlow\Exceptions\ForeignDomesticNetFlowException;
use App\Features\ForeignDomesticNetFlow\Services\ForeignDomesticNetFlowService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class ForeignDomesticNetFlowController extends Controller
{
    public function __construct(
        private readonly ForeignDomesticNetFlowService $service,
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
                'points' => $this->service->getNetFlow(
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
        } catch (ForeignDomesticNetFlowException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }
}

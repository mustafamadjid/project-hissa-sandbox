<?php

namespace App\Features\DominanceRatio\Http\Controller;

use App\Features\DominanceRatio\Services\DominanceRatioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

final class DominanceRatioController
{
    public function __construct(
        private readonly DominanceRatioService $service,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'stock_code' => ['nullable', 'string', 'max:10'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        return response()->json([
            'items' => $this->service->getDominanceRatio(
                $validated['start_date'],
                $validated['end_date'],
                $validated['stock_code'] ?? null,
            ),
            'meta' => [
                'ratio_basis' => 'transaction_value',
                'unit' => 'percent',
            ],
        ]);
    }
}

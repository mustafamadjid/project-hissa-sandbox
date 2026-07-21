<?php

namespace App\Features\TopAccumulationDistribution\Http\Controller;

use App\Features\TopAccumulationDistribution\Services\TopAccumulationDistributionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

final class TopAccumulationDistributionController
{
    public function __construct(
        private readonly TopAccumulationDistributionService $service,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $limit = (int) ($validated['limit'] ?? 10);

        return response()->json([
            'period' => [
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ],
            'items' => $this->service->getTopAccumulationDistribution(
                $validated['start_date'],
                $validated['end_date'],
                $limit,
            ),
            'meta' => [
                'limit' => $limit,
                'aggregation' => 'sum',
                'unit' => 'IDR',
            ],
        ]);
    }
}
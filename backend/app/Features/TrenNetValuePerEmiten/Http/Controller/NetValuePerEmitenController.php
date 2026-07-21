<?php

namespace App\Features\TrenNetValuePerEmiten\Http\Controller;

use App\Features\TrenNetValuePerEmiten\Services\NetValuePerEmitenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

final class NetValuePerEmitenController
{
    public function __construct(
        private readonly NetValuePerEmitenService $service
    ) {}

    public function __invoke(Request $request, string $stockCode): JsonResponse
    {
        $validator = Validator::make($request->query(), [
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        $data = $this->service->getNetValuePerEmiten(
            $stockCode,
            $validated['start_date'],
            $validated['end_date']
        );

        return response()->json([
            'stock_code' => $stockCode,
            'period'     => [
                'start_date' => $validated['start_date'],
                'end_date'   => $validated['end_date']
            ],
            'points'     => $data,
            'meta'       => [
                'unit'       => 'IDR',
            ],
        ]);
    }
}
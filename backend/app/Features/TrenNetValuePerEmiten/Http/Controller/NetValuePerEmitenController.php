<?php

namespace App\Features\TrenNetValuePerEmiten\Http\Controller;

use App\Features\TrenNetValuePerEmiten\Services\NetValuePerEmitenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class NetValuePerEmitenController
{
    public function __construct(
        private readonly NetValuePerEmitenService $service
    ) {}

    public function __invoke(Request $request, string $stockCode): JsonResponse
    {
        $data = $this->service->getNetValuePerEmiten(
            $stockCode,
            $request->query('start_date'),
            $request->query('end_date')
        );

        return response()->json([
            'stock_code' => $stockCode,
            'period'     => [
                'start_date' => $request->query('start_date'),
                'end_date'   => $request->query('end_date')
            ],
            'points'     => $data,
            'meta'       => [
                'unit'       => 'IDR',
            ],
        ]);
    }
}

<?php

namespace App\Features\TrenNetValuePerEmiten\Http\Controller;

use App\Features\TrenNetValuePerEmiten\Exceptions\NetValuePerEmitenException;
use App\Features\TrenNetValuePerEmiten\Services\NetValuePerEmitenService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class NetValuePerEmitenController extends Controller
{
    public function __construct(
        private readonly NetValuePerEmitenService $service
    ) {}

    public function __invoke(Request $request, string $stockCode): JsonResponse
    {
        try {
            $validator = Validator::make($request->query(), [
                'start_date' => ['required', 'date_format:Y-m-d'],
                'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            ]);

            $validated = $validator->validate();

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
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (NetValuePerEmitenException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        }
    }
}

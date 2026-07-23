<?php

namespace App\Features\TopAccumulationDistribution\Http\Controller;

use App\Features\TopAccumulationDistribution\Exceptions\TopAccumulationDistributionException;
use App\Features\TopAccumulationDistribution\Services\TopAccumulationDistributionService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class TopAccumulationDistributionController extends Controller
{
    public function __construct(
        private readonly TopAccumulationDistributionService $service,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->query(), [
                'start_date' => ['required', 'date_format:Y-m-d'],
                'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
                'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            ]);

            $validated = $validator->validate();
            $limit = (int) ($validated['limit'] ?? 10);
            $items = $this->service->getTopAccumulationDistribution(
                $validated['start_date'],
                $validated['end_date'],
                $limit,
            );

            return response()->json([
                'period' => [
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                ],
                'items' => $items,
                'meta' => [
                    'limit' => $limit,
                    'aggregation' => 'sum',
                    'unit' => 'IDR',
                ],
            ]);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (TopAccumulationDistributionException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return $this->serverError('Terjadi kesalahan tak terduga.');
        }
    }
}

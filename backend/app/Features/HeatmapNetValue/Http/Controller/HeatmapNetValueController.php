<?php

namespace App\Features\HeatmapNetValue\Http\Controller;

use App\Features\HeatmapNetValue\Exceptions\HeatmapNetValueException;
use App\Features\HeatmapNetValue\Services\HeatmapNetValueService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class HeatmapNetValueController extends Controller
{
    public function __construct(
        private readonly HeatmapNetValueService $service,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_date' => ['required', 'date_format:Y-m-d'],
                'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            ]);
            $heatmapData = $this->service->getHeatmapData(
                $validated['start_date'],
                $validated['end_date'],
            );

            return response()->json($heatmapData);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (HeatmapNetValueException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return $this->serverError('Terjadi kesalahan tak terduga.');
        }
    }
}

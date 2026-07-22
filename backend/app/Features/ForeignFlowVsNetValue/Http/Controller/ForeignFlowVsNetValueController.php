<?php

namespace App\Features\ForeignFlowVsNetValue\Http\Controller;

use App\Features\ForeignFlowVsNetValue\Exceptions\ForeignFlowVsNetValueException;
use App\Features\ForeignFlowVsNetValue\Services\ForeignFlowVsNetValueService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class ForeignFlowVsNetValueController extends Controller
{
    public function __construct(
        private readonly ForeignFlowVsNetValueService $service,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->query(), [
                'start_date' => ['required', 'date_format:Y-m-d'],
                'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
                'aggregation' => ['nullable', 'string', 'in:sum'],
                'stock_codes' => ['nullable', 'string', 'regex:/^[A-Za-z0-9,\s]+$/'],
                'min_abs_value' => ['nullable', 'numeric', 'min:0'],
                'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            ]);

            $validated = $validator->validate();
            $stockCodes = $this->stockCodes($validated['stock_codes'] ?? null);
            $minAbsValue = isset($validated['min_abs_value']) ? (float) $validated['min_abs_value'] : null;

            $items = $this->service->getScatterData(
                $validated['start_date'],
                $validated['end_date'],
                $stockCodes,
                $minAbsValue,
            );

            if (isset($validated['limit'])) {
                $items = array_slice($items, 0, (int) $validated['limit']);
            }

            return response()->json([
                'period' => [
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date'],
                ],
                'items' => $items,
                'meta' => [
                    'unit' => 'IDR',
                    'aggregation' => $validated['aggregation'] ?? 'sum',
                ],
            ]);
        } catch (ValidationException $exception) {
            return $this->validationError($exception);
        } catch (ForeignFlowVsNetValueException $exception) {
            report($exception);

            return $this->serverError($exception->getMessage());
        } catch (\Throwable $exception) {
            report($exception);

            return $this->serverError('Terjadi kesalahan tak terduga.');
        }
    }

    private function stockCodes(?string $stockCodes): ?array
    {
        if ($stockCodes === null) {
            return null;
        }

        $normalized = collect(explode(',', $stockCodes))
            ->map(fn ($code) => strtoupper(trim($code)))
            ->filter()
            ->unique()
            ->values();

        if ($normalized->contains(fn (string $code) => strlen($code) > 10)) {
            throw ValidationException::withMessages([
                'stock_codes' => ['Setiap stock code maksimal 10 karakter.'],
            ]);
        }

        return $normalized->isEmpty() ? null : $normalized->all();
    }
}

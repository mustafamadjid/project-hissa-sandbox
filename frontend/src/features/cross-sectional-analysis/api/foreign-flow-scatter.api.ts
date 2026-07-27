import { httpClient, toApiError, withRequestSignal } from "@/shared/api/http-client";
import { sanitizeQueryParams } from "@/shared/api/query-params";
import { foreignFlowScatterResponseSchema } from "@/features/cross-sectional-analysis/schemas/foreign-flow-scatter.schema";
import type { ForeignFlowScatterParams, ForeignFlowScatterResponseDto } from "@/features/cross-sectional-analysis/types/foreign-flow-scatter.types";

export async function fetchForeignFlowScatter(params: ForeignFlowScatterParams, signal?: AbortSignal): Promise<ForeignFlowScatterResponseDto> {
  try {
    const response = await httpClient.get<unknown>("/market/foreign-flow-net-value-scatter", {
      params: sanitizeQueryParams({
        start_date: params.start_date,
        end_date: params.end_date,
        aggregation: params.aggregation,
        stock_codes: params.stock_codes,
        min_abs_value: params.min_abs_value,
        limit: params.limit,
      }),
      ...withRequestSignal(signal),
    });
    return foreignFlowScatterResponseSchema.parse(response.data);
  } catch (error) {
    throw toApiError(error);
  }
}

import { httpClient, toApiError, withRequestSignal } from "@/shared/api/http-client";
import { sanitizeQueryParams } from "@/shared/api/query-params";
import { dominanceRatioResponseSchema } from "@/features/market-overview/schemas/dominance-ratio.schema";
import type {
  DominanceRatioParams,
  DominanceRatioResponseDto,
} from "@/features/market-overview/types/dominance-ratio.types";

export async function fetchDominanceRatio(
  params: DominanceRatioParams,
  signal?: AbortSignal,
): Promise<DominanceRatioResponseDto> {
  try {
    const response = await httpClient.get<unknown>("/market/dominance-ratio", {
      params: sanitizeQueryParams({
        start_date: params.start_date,
        end_date: params.end_date,
        stock_code: params.stock_code,
        granularity: params.granularity,
      }),
      ...withRequestSignal(signal),
    });

    return dominanceRatioResponseSchema.parse(response.data);
  } catch (error) {
    throw toApiError(error);
  }
}

import { httpClient, toApiError, withRequestSignal } from "@/shared/api/http-client";
import { sanitizeQueryParams } from "@/shared/api/query-params";
import { netValueRankingResponseSchema } from "@/features/market-overview/schemas/net-value-ranking.schema";
import type {
  NetValueRankingParams,
  NetValueRankingResponseDto,
} from "@/features/market-overview/types/net-value-ranking.types";

export async function fetchNetValueRanking(
  params: NetValueRankingParams,
  signal?: AbortSignal,
): Promise<NetValueRankingResponseDto> {
  try {
    const response = await httpClient.get<unknown>("/market/net-value-ranking", {
      params: sanitizeQueryParams({
        start_date: params.start_date,
        end_date: params.end_date,
        limit: params.limit,
      }),
      ...withRequestSignal(signal),
    });

    return netValueRankingResponseSchema.parse(response.data);
  } catch (error) {
    throw toApiError(error);
  }
}

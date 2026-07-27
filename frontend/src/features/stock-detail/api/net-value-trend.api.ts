import { httpClient, toApiError, withRequestSignal } from "@/shared/api/http-client";
import { sanitizeQueryParams } from "@/shared/api/query-params";
import { netValueTrendResponseSchema } from "@/features/stock-detail/schemas/net-value-trend.schema";
import type {
  NetValueTrendParams,
  NetValueTrendResponseDto,
} from "@/features/stock-detail/types/net-value-trend.types";

export async function fetchNetValueTrend(
  params: NetValueTrendParams,
  signal?: AbortSignal,
): Promise<NetValueTrendResponseDto> {
  try {
    const response = await httpClient.get<unknown>(
      `/tren-net-value/${encodeURIComponent(params.stock_code)}`,
      {
        params: sanitizeQueryParams({
          start_date: params.start_date,
          end_date: params.end_date,
        }),
        ...withRequestSignal(signal),
      },
    );

    const parsed = netValueTrendResponseSchema.parse(response.data);
    return {
      stock_code: parsed.stock_code,
      period: parsed.period,
      points: parsed.points,
      meta: {
        unit: parsed.meta.unit,
        ...(parsed.meta.updated_at !== undefined
          ? { updated_at: parsed.meta.updated_at }
          : {}),
      },
    };
  } catch (error) {
    throw toApiError(error);
  }
}

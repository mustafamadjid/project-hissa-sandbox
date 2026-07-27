import { httpClient, toApiError, withRequestSignal } from "@/shared/api/http-client";
import { sanitizeQueryParams } from "@/shared/api/query-params";
import { netValueHeatmapResponseSchema } from "@/features/market-scanner/schemas/net-value-heatmap.schema";
import type { NetValueHeatmapParams, NetValueHeatmapResponseDto } from "@/features/market-scanner/types/net-value-heatmap.types";

export async function fetchNetValueHeatmap(params: NetValueHeatmapParams, signal?: AbortSignal): Promise<NetValueHeatmapResponseDto> {
  try {
    const response = await httpClient.get<unknown>("/market/net-value-heatmap", {
      params: sanitizeQueryParams({ start_date: params.start_date, end_date: params.end_date }),
      ...withRequestSignal(signal),
    });
    return netValueHeatmapResponseSchema.parse(response.data);
  } catch (error) {
    throw toApiError(error);
  }
}

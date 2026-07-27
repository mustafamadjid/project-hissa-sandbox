import { httpClient, toApiError, withRequestSignal } from "@/shared/api/http-client";
import { sanitizeQueryParams } from "@/shared/api/query-params";
import { cumulativeNetValueResponseSchema } from "@/features/stock-detail/schemas/cumulative-net-value.schema";
import type { CumulativeNetValueParams, CumulativeNetValueResponseDto } from "@/features/stock-detail/types/cumulative-net-value.types";
export async function fetchCumulativeNetValue(params: CumulativeNetValueParams, signal?: AbortSignal): Promise<CumulativeNetValueResponseDto> { try { const response = await httpClient.get<unknown>(`/market/stocks/${encodeURIComponent(params.stock_code)}/cumulative-net-value`, { params: sanitizeQueryParams({ start_date: params.start_date, end_date: params.end_date, reset: "start_of_period" }), ...withRequestSignal(signal) }); return cumulativeNetValueResponseSchema.parse(response.data); } catch (error) { throw toApiError(error); } }

import { httpClient, toApiError, withRequestSignal } from "@/shared/api/http-client";
import { sanitizeQueryParams } from "@/shared/api/query-params";
import { foreignGrossFlowResponseSchema } from "@/features/stock-detail/schemas/foreign-gross-flow.schema";
import type { ForeignGrossFlowParams, ForeignGrossFlowResponseDto } from "@/features/stock-detail/types/foreign-gross-flow.types";
export async function fetchForeignGrossFlow(params: ForeignGrossFlowParams, signal?: AbortSignal): Promise<ForeignGrossFlowResponseDto> { try { const response = await httpClient.get<unknown>(`/market/stocks/${encodeURIComponent(params.stock_code)}/foreign/gross-flow`, { params: sanitizeQueryParams({ start_date: params.start_date, end_date: params.end_date, granularity: "daily" }), ...withRequestSignal(signal) }); return foreignGrossFlowResponseSchema.parse(response.data); } catch (error) { throw toApiError(error); } }

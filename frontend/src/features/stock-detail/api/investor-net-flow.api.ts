import { httpClient, toApiError, withRequestSignal } from "@/shared/api/http-client";
import { sanitizeQueryParams } from "@/shared/api/query-params";
import { investorNetFlowResponseSchema } from "@/features/stock-detail/schemas/investor-net-flow.schema";
import type {
  InvestorNetFlowParams,
  InvestorNetFlowResponseDto,
} from "@/features/stock-detail/types/investor-net-flow.types";

export async function fetchInvestorNetFlow(
  params: InvestorNetFlowParams,
  signal?: AbortSignal,
): Promise<InvestorNetFlowResponseDto> {
  try {
    const response = await httpClient.get<unknown>(
      `/market/stocks/${encodeURIComponent(params.stock_code)}/investor/net-flow`,
      {
        params: sanitizeQueryParams({
          start_date: params.start_date,
          end_date: params.end_date,
          granularity: params.granularity ?? "daily",
        }),
        ...withRequestSignal(signal),
      },
    );

    return investorNetFlowResponseSchema.parse(response.data);
  } catch (error) {
    throw toApiError(error);
  }
}

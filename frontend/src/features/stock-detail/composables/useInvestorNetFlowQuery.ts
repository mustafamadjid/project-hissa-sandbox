import { computed, toValue, type MaybeRefOrGetter } from "vue";
import { useQuery, keepPreviousData } from "@tanstack/vue-query";
import { fetchInvestorNetFlow } from "@/features/stock-detail/api/investor-net-flow.api";
import { mapInvestorNetFlowToChartModel } from "@/features/stock-detail/mappers/mapInvestorNetFlowToChartModel";
import type { InvestorNetFlowParams } from "@/features/stock-detail/types/investor-net-flow.types";
import { isValidDateRange } from "@/shared/formatters/date";
import { getApiErrorMessage } from "@/shared/api/http-client";
import { isValidStockCode } from "@/shared/utils/stock-code";

export const investorNetFlowKeys = {
  all: ["investor-net-flow"] as const,
  detail: (params: InvestorNetFlowParams) =>
    [...investorNetFlowKeys.all, params] as const,
};

export function isValidInvestorNetFlowParams(
  params: InvestorNetFlowParams,
): boolean {
  return (
    isValidStockCode(params.stock_code) &&
    isValidDateRange(params.start_date, params.end_date)
  );
}

export function useInvestorNetFlowQuery(
  params: MaybeRefOrGetter<InvestorNetFlowParams>,
) {
  const query = useQuery({
    queryKey: computed(() => investorNetFlowKeys.detail(toValue(params))),
    queryFn: ({ signal }) => fetchInvestorNetFlow(toValue(params), signal),
    enabled: computed(() => isValidInvestorNetFlowParams(toValue(params))),
    staleTime: 5 * 60 * 1000,
    placeholderData: keepPreviousData,
  });

  const chartModel = computed(() =>
    query.data.value ? mapInvestorNetFlowToChartModel(query.data.value) : null,
  );

  const errorMessage = computed(() =>
    query.error.value ? getApiErrorMessage(query.error.value) : null,
  );

  return {
    ...query,
    chartModel,
    errorMessage,
  };
}

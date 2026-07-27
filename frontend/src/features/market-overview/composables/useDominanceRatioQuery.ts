import { computed, toValue, type MaybeRefOrGetter } from "vue";
import { useQuery, keepPreviousData } from "@tanstack/vue-query";
import { fetchDominanceRatio } from "@/features/market-overview/api/dominance-ratio.api";
import { mapDominanceRatioToChartModel } from "@/features/market-overview/mappers/mapDominanceRatioToChartModel";
import type { DominanceRatioParams } from "@/features/market-overview/types/dominance-ratio.types";
import { isValidDateRange } from "@/shared/formatters/date";
import { getApiErrorMessage } from "@/shared/api/http-client";
import { isValidStockCode } from "@/shared/utils/stock-code";

export const dominanceRatioKeys = {
  all: ["dominance-ratio"] as const,
  list: (params: DominanceRatioParams) =>
    [...dominanceRatioKeys.all, params] as const,
};

export function isValidDominanceRatioParams(
  params: DominanceRatioParams,
): boolean {
  if (!isValidDateRange(params.start_date, params.end_date)) {
    return false;
  }
  if (params.stock_code !== undefined && params.stock_code !== "") {
    return isValidStockCode(params.stock_code);
  }
  return true;
}

export function useDominanceRatioQuery(
  params: MaybeRefOrGetter<DominanceRatioParams>,
) {
  const query = useQuery({
    queryKey: computed(() => dominanceRatioKeys.list(toValue(params))),
    queryFn: ({ signal }) => fetchDominanceRatio(toValue(params), signal),
    enabled: computed(() => isValidDominanceRatioParams(toValue(params))),
    staleTime: 5 * 60 * 1000,
    placeholderData: keepPreviousData,
  });

  const chartModel = computed(() =>
    query.data.value ? mapDominanceRatioToChartModel(query.data.value) : null,
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

import { computed, toValue, type MaybeRefOrGetter } from "vue";
import { useQuery, keepPreviousData } from "@tanstack/vue-query";
import { fetchNetValueTrend } from "@/features/stock-detail/api/net-value-trend.api";
import { mapNetValueTrendToChartModel } from "@/features/stock-detail/mappers/mapNetValueTrendToChartModel";
import type { NetValueTrendParams } from "@/features/stock-detail/types/net-value-trend.types";
import { isValidDateRange } from "@/shared/formatters/date";
import { getApiErrorMessage } from "@/shared/api/http-client";
import { isValidStockCode } from "@/shared/utils/stock-code";

export const netValueTrendKeys = {
  all: ["net-value-trend"] as const,
  detail: (params: NetValueTrendParams) =>
    [...netValueTrendKeys.all, params] as const,
};

export function isValidNetValueTrendParams(
  params: NetValueTrendParams,
): boolean {
  return (
    isValidStockCode(params.stock_code) &&
    isValidDateRange(params.start_date, params.end_date)
  );
}

export function useNetValueTrendQuery(
  params: MaybeRefOrGetter<NetValueTrendParams>,
) {
  const query = useQuery({
    queryKey: computed(() => netValueTrendKeys.detail(toValue(params))),
    queryFn: ({ signal }) => fetchNetValueTrend(toValue(params), signal),
    enabled: computed(() => isValidNetValueTrendParams(toValue(params))),
    staleTime: 5 * 60 * 1000,
    placeholderData: keepPreviousData,
  });

  const chartModel = computed(() =>
    query.data.value ? mapNetValueTrendToChartModel(query.data.value) : null,
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

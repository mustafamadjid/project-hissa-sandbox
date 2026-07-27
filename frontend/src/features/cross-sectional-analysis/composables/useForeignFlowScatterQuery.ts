import { computed, toValue, type MaybeRefOrGetter } from "vue";
import { keepPreviousData, useQuery } from "@tanstack/vue-query";
import { fetchForeignFlowScatter } from "@/features/cross-sectional-analysis/api/foreign-flow-scatter.api";
import { mapForeignFlowScatterToChartModel } from "@/features/cross-sectional-analysis/mappers/mapForeignFlowScatterToChartModel";
import type { ForeignFlowScatterParams } from "@/features/cross-sectional-analysis/types/foreign-flow-scatter.types";
import { getApiErrorMessage } from "@/shared/api/http-client";
import { isValidDateRange } from "@/shared/formatters/date";

export const foreignFlowScatterKeys = {
  all: ["foreign-flow-scatter"] as const,
  list: (params: ForeignFlowScatterParams) => [...foreignFlowScatterKeys.all, params] as const,
};

export function isValidForeignFlowScatterParams(params: ForeignFlowScatterParams): boolean {
  return isValidDateRange(params.start_date, params.end_date)
    && (params.aggregation === undefined || params.aggregation === "sum")
    && (params.min_abs_value === undefined || params.min_abs_value >= 0)
    && (params.limit === undefined || (Number.isInteger(params.limit) && params.limit >= 1 && params.limit <= 100));
}

export function useForeignFlowScatterQuery(params: MaybeRefOrGetter<ForeignFlowScatterParams>) {
  const query = useQuery({
    queryKey: computed(() => foreignFlowScatterKeys.list(toValue(params))),
    queryFn: ({ signal }) => fetchForeignFlowScatter(toValue(params), signal),
    enabled: computed(() => isValidForeignFlowScatterParams(toValue(params))),
    staleTime: 5 * 60 * 1000,
    placeholderData: keepPreviousData,
  });
  return {
    ...query,
    chartModel: computed(() => query.data.value ? mapForeignFlowScatterToChartModel(query.data.value) : null),
    errorMessage: computed(() => query.error.value ? getApiErrorMessage(query.error.value) : null),
  };
}

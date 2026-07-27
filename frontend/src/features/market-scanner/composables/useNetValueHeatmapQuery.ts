import { computed, toValue, type MaybeRefOrGetter } from "vue";
import { keepPreviousData, useQuery } from "@tanstack/vue-query";
import { fetchNetValueHeatmap } from "@/features/market-scanner/api/net-value-heatmap.api";
import { mapNetValueHeatmapToChartModel } from "@/features/market-scanner/mappers/mapNetValueHeatmapToChartModel";
import type { NetValueHeatmapParams } from "@/features/market-scanner/types/net-value-heatmap.types";
import { getApiErrorMessage } from "@/shared/api/http-client";
import { isValidDateRange } from "@/shared/formatters/date";

export const netValueHeatmapKeys = { all: ["net-value-heatmap"] as const, detail: (params: NetValueHeatmapParams) => ["net-value-heatmap", params] as const };

export function useNetValueHeatmapQuery(params: MaybeRefOrGetter<NetValueHeatmapParams>) {
  const query = useQuery({
    queryKey: computed(() => netValueHeatmapKeys.detail(toValue(params))),
    queryFn: ({ signal }) => fetchNetValueHeatmap(toValue(params), signal),
    enabled: computed(() => isValidDateRange(toValue(params).start_date, toValue(params).end_date)),
    staleTime: 5 * 60 * 1000,
    placeholderData: keepPreviousData,
  });
  return {
    ...query,
    chartModel: computed(() => query.data.value ? mapNetValueHeatmapToChartModel(query.data.value) : null),
    errorMessage: computed(() => query.error.value ? getApiErrorMessage(query.error.value) : null),
  };
}

import { computed, toValue, type MaybeRefOrGetter } from "vue";
import { useQuery, keepPreviousData } from "@tanstack/vue-query";
import { fetchNetValueRanking } from "@/features/market-overview/api/net-value-ranking.api";
import { mapNetValueRankingToChartModel } from "@/features/market-overview/mappers/mapNetValueRankingToChartModel";
import type { NetValueRankingParams } from "@/features/market-overview/types/net-value-ranking.types";
import { isValidDateRange } from "@/shared/formatters/date";
import { getApiErrorMessage } from "@/shared/api/http-client";

export const netValueRankingKeys = {
  all: ["net-value-ranking"] as const,
  list: (params: NetValueRankingParams) =>
    [...netValueRankingKeys.all, params] as const,
};

export function isValidNetValueRankingParams(
  params: NetValueRankingParams,
): boolean {
  if (!isValidDateRange(params.start_date, params.end_date)) {
    return false;
  }
  if (params.limit !== undefined && (params.limit < 1 || params.limit > 100)) {
    return false;
  }
  return true;
}

export function useNetValueRankingQuery(
  params: MaybeRefOrGetter<NetValueRankingParams>,
) {
  const query = useQuery({
    queryKey: computed(() => netValueRankingKeys.list(toValue(params))),
    queryFn: ({ signal }) => fetchNetValueRanking(toValue(params), signal),
    enabled: computed(() => isValidNetValueRankingParams(toValue(params))),
    staleTime: 5 * 60 * 1000,
    placeholderData: keepPreviousData,
  });

  const chartModel = computed(() =>
    query.data.value ? mapNetValueRankingToChartModel(query.data.value) : null,
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

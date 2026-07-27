import { computed, toValue, type MaybeRefOrGetter } from "vue";
import { keepPreviousData, useQuery } from "@tanstack/vue-query";
import { fetchCumulativeNetValue } from "@/features/stock-detail/api/cumulative-net-value.api";
import { mapCumulativeNetValueToChartModel } from "@/features/stock-detail/mappers/mapCumulativeNetValueToChartModel";
import type { CumulativeNetValueParams } from "@/features/stock-detail/types/cumulative-net-value.types";
import { getApiErrorMessage } from "@/shared/api/http-client";
import { isValidDateRange } from "@/shared/formatters/date";
import { isValidStockCode } from "@/shared/utils/stock-code";
export function useCumulativeNetValueQuery(params: MaybeRefOrGetter<CumulativeNetValueParams>) { const query = useQuery({ queryKey: computed(() => ["cumulative-net-value", toValue(params)] as const), queryFn: ({ signal }) => fetchCumulativeNetValue(toValue(params), signal), enabled: computed(() => isValidStockCode(toValue(params).stock_code) && isValidDateRange(toValue(params).start_date, toValue(params).end_date)), staleTime: 5 * 60 * 1000, placeholderData: keepPreviousData }); return { ...query, chartModel: computed(() => query.data.value ? mapCumulativeNetValueToChartModel(query.data.value) : null), errorMessage: computed(() => query.error.value ? getApiErrorMessage(query.error.value) : null) }; }

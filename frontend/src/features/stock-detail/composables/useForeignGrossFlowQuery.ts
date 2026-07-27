import { computed, toValue, type MaybeRefOrGetter } from "vue";
import { keepPreviousData, useQuery } from "@tanstack/vue-query";
import { fetchForeignGrossFlow } from "@/features/stock-detail/api/foreign-gross-flow.api";
import { mapForeignGrossFlowToChartModel } from "@/features/stock-detail/mappers/mapForeignGrossFlowToChartModel";
import type { ForeignGrossFlowParams } from "@/features/stock-detail/types/foreign-gross-flow.types";
import { getApiErrorMessage } from "@/shared/api/http-client";
import { isValidDateRange } from "@/shared/formatters/date";
import { isValidStockCode } from "@/shared/utils/stock-code";
export function useForeignGrossFlowQuery(params: MaybeRefOrGetter<ForeignGrossFlowParams>) { const query = useQuery({ queryKey: computed(() => ["foreign-gross-flow", toValue(params)] as const), queryFn: ({ signal }) => fetchForeignGrossFlow(toValue(params), signal), enabled: computed(() => isValidStockCode(toValue(params).stock_code) && isValidDateRange(toValue(params).start_date, toValue(params).end_date)), staleTime: 5 * 60 * 1000, placeholderData: keepPreviousData }); return { ...query, chartModel: computed(() => query.data.value ? mapForeignGrossFlowToChartModel(query.data.value) : null), errorMessage: computed(() => query.error.value ? getApiErrorMessage(query.error.value) : null) }; }

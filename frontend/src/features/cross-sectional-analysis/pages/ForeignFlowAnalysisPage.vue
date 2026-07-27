<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import ForeignFlowScatterChart from "@/features/cross-sectional-analysis/components/ForeignFlowScatterChart.vue";
import { useForeignFlowScatterQuery } from "@/features/cross-sectional-analysis/composables/useForeignFlowScatterQuery";
import DateRangeFilter from "@/shared/components/filters/DateRangeFilter.vue";
import FilterBar from "@/shared/components/filters/FilterBar.vue";
import PageHeader from "@/shared/components/layout/PageHeader.vue";
import type { ForeignFlowScatterParams } from "@/features/cross-sectional-analysis/types/foreign-flow-scatter.types";
import { isValidDateRange } from "@/shared/formatters/date";
import { defaultEndDate, defaultStartDate } from "@/shared/utils/date-defaults";

function readString(value: unknown, fallback: string): string { return typeof value === "string" && value.length > 0 ? value : fallback; }
function readNumber(value: unknown): number | undefined { const number = typeof value === "string" && value !== "" ? Number(value) : undefined; return number !== undefined && Number.isFinite(number) && number >= 0 ? number : undefined; }
const route = useRoute();
const router = useRouter();
const startDate = ref(readString(route.query.start_date, defaultStartDate()));
const endDate = ref(readString(route.query.end_date, defaultEndDate()));
const minAbsValue = ref(readNumber(route.query.min_abs_value));
const limit = ref(readNumber(route.query.limit) ?? 100);
watch([startDate, endDate, minAbsValue, limit], () => {
  const query: Record<string, string> = { start_date: startDate.value, end_date: endDate.value, aggregation: "sum", limit: String(limit.value) };
  if (minAbsValue.value !== undefined) query.min_abs_value = String(minAbsValue.value);
  void router.replace({ name: "foreign-flow-analysis", query });
});
const params = computed(() => {
  const base: ForeignFlowScatterParams = { start_date: startDate.value, end_date: endDate.value, aggregation: "sum", limit: limit.value };
  if (minAbsValue.value !== undefined) base.min_abs_value = minAbsValue.value;
  return base;
});
const scatterQuery = useForeignFlowScatterQuery(params);
const filterValid = computed(() => isValidDateRange(startDate.value, endDate.value) && limit.value >= 1 && limit.value <= 100);
function openStockDetail(stockCode: string): void { void router.push({ name: "stock-detail", params: { stockCode }, query: { start_date: startDate.value, end_date: endDate.value } }); }
</script>

<template>
  <div class="space-y-4">
    <PageHeader title="Analisis Foreign Flow" subtitle="Bandingkan foreign net flow dan net value lintas saham pada periode terpilih." />
    <FilterBar title="Filter analisis">
      <DateRangeFilter v-model:start-date="startDate" v-model:end-date="endDate" id-prefix="foreign-flow" class="flex-1" />
      <label class="grid gap-1 text-sm font-medium text-gray-700 dark:text-gray-200">Nilai minimum (opsional)<input v-model.number="minAbsValue" class="input" type="number" min="0" inputmode="decimal" placeholder="Tanpa batas"></label>
      <label class="grid gap-1 text-sm font-medium text-gray-700 dark:text-gray-200">Limit<input v-model.number="limit" class="input" type="number" min="1" max="100" inputmode="numeric"></label>
    </FilterBar>
    <p v-if="!filterValid" class="status-warning">Rentang tanggal atau limit tidak valid. Limit harus 1–100.</p>
    <ForeignFlowScatterChart :model="scatterQuery.chartModel.value" :loading="scatterQuery.isPending.value" :fetching="scatterQuery.isFetching.value" :error="scatterQuery.errorMessage.value" @retry="scatterQuery.refetch()" @select-stock="openStockDetail" />
  </div>
</template>

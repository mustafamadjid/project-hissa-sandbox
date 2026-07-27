<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import PageHeader from "@/shared/components/layout/PageHeader.vue";
import FilterBar from "@/shared/components/filters/FilterBar.vue";
import DateRangeFilter from "@/shared/components/filters/DateRangeFilter.vue";
import NetValueHeatmapChart from "@/features/market-scanner/components/NetValueHeatmapChart.vue";
import { useNetValueHeatmapQuery } from "@/features/market-scanner/composables/useNetValueHeatmapQuery";
import { defaultEndDate, defaultStartDate } from "@/shared/utils/date-defaults";
import { isValidDateRange } from "@/shared/formatters/date";
function readQueryString(value: unknown, fallback: string): string { return typeof value === "string" && value.length > 0 ? value : fallback; }
const route = useRoute();
const router = useRouter();
const startDate = ref(readQueryString(route.query.start_date, defaultStartDate()));
const endDate = ref(readQueryString(route.query.end_date, defaultEndDate()));
watch([startDate, endDate], () => { void router.replace({ name: "market-scanner", query: { start_date: startDate.value, end_date: endDate.value } }); });
const params = computed(() => ({ start_date: startDate.value, end_date: endDate.value }));
const heatmapQuery = useNetValueHeatmapQuery(params);
const filterValid = computed(() => isValidDateRange(startDate.value, endDate.value));
function openStockDetail(stockCode: string): void { void router.push({ name: "stock-detail", params: { stockCode }, query: { start_date: startDate.value, end_date: endDate.value } }); }
</script>
<template>
  <div class="space-y-4">
    <PageHeader title="Market Scanner" subtitle="Heatmap net value untuk membaca akumulasi dan distribusi lintas saham." />
    <FilterBar title="Filter periode">
      <DateRangeFilter v-model:start-date="startDate" v-model:end-date="endDate" id-prefix="scanner" class="flex-1" />
    </FilterBar>
    <p v-if="!filterValid" class="status-warning">Rentang tanggal tidak valid. Pastikan format YYYY-MM-DD dan tanggal akhir tidak lebih awal dari tanggal mulai.</p>
    <NetValueHeatmapChart :model="heatmapQuery.chartModel.value" :loading="heatmapQuery.isPending.value" :fetching="heatmapQuery.isFetching.value" :error="heatmapQuery.errorMessage.value" @retry="heatmapQuery.refetch()" @select-stock="openStockDetail" />
  </div>
</template>

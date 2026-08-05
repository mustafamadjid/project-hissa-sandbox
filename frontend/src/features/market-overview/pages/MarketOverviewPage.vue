<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import PageHeader from "@/shared/components/layout/PageHeader.vue";
import DashboardGrid from "@/shared/components/layout/DashboardGrid.vue";
import FilterBar from "@/shared/components/filters/FilterBar.vue";
import DateRangeFilter from "@/shared/components/filters/DateRangeFilter.vue";
import LimitSelect from "@/shared/components/filters/LimitSelect.vue";
import StockCodeSelect from "@/shared/components/filters/StockCodeSelect.vue";
import { useDebouncedRef } from "@/shared/composables/useDebouncedRef";
import NetValueRankingChart from "@/features/market-overview/components/NetValueRankingChart.vue";
import DominanceRatioChart from "@/features/market-overview/components/DominanceRatioChart.vue";
import { useNetValueRankingQuery } from "@/features/market-overview/composables/useNetValueRankingQuery";
import { useDominanceRatioQuery } from "@/features/market-overview/composables/useDominanceRatioQuery";
import {
  defaultEndDate,
  defaultStartDate,
} from "@/shared/utils/date-defaults";
import { isValidDateRange } from "@/shared/formatters/date";
import {
  normalizeStockCode,
} from "@/shared/utils/stock-code";

const route = useRoute();
const router = useRouter();

function readQueryString(value: unknown, fallback: string): string {
  return typeof value === "string" && value.length > 0 ? value : fallback;
}

function readQueryNumber(value: unknown, fallback: number): number {
  if (typeof value === "string" && value.length > 0) {
    const parsed = Number(value);
    if (Number.isFinite(parsed)) return parsed;
  }
  return fallback;
}

const startDate = ref(readQueryString(route.query.start_date, defaultStartDate()));
const endDate = ref(readQueryString(route.query.end_date, defaultEndDate()));
const limit = ref(readQueryNumber(route.query.limit, 10));
const granularity = ref<"daily" | "weekly" | "monthly">(
  route.query.granularity === "weekly" || route.query.granularity === "monthly"
    ? route.query.granularity
    : "daily",
);
const stockCode = ref(
  typeof route.query.stock_code === "string"
    ? normalizeStockCode(route.query.stock_code)
    : "",
);
const debouncedStockCode = useDebouncedRef(stockCode, 300);

watch(
  [startDate, endDate, limit, granularity, debouncedStockCode],
  () => {
    const query: Record<string, string> = {
      start_date: startDate.value,
      end_date: endDate.value,
      limit: String(limit.value),
      granularity: granularity.value,
    };
    if (debouncedStockCode.value) {
      query.stock_code = debouncedStockCode.value;
    }
    void router.replace({ query });
  },
  { deep: false },
);

const rankingParams = computed(() => ({
  start_date: startDate.value,
  end_date: endDate.value,
  limit: limit.value,
}));

const dominanceParams = computed(() => {
  return {
    start_date: startDate.value,
    end_date: endDate.value,
    stock_code: debouncedStockCode.value,
    granularity: granularity.value,
  };
});

const rankingQuery = useNetValueRankingQuery(rankingParams);
const dominanceQuery = useDominanceRatioQuery(dominanceParams);

const filterValid = computed(() =>
  isValidDateRange(startDate.value, endDate.value),
);

function openStockDetail(code: string): void {
  void router.push({
    name: "stock-detail",
    params: { stockCode: code },
    query: {
      start_date: startDate.value,
      end_date: endDate.value,
    },
  });
}
</script>

<template>
  <div class="space-y-4">
    <PageHeader
      title="Market Overview"
      subtitle="Ringkasan akumulasi/distribusi pasar dan komposisi dominance ratio investor."
    />

    <FilterBar title="Filter periode">
      <DateRangeFilter
        v-model:start-date="startDate"
        v-model:end-date="endDate"
        id-prefix="market"
        class="flex-1"
      />
      <LimitSelect v-model="limit" class="w-full sm:w-32" label="Limit ranking" />
      <StockCodeSelect
        v-model="stockCode"
        class="w-full sm:w-40"
        label="Saham"
        placeholder="UNVR"
      />
      <div class="w-full sm:w-36">
        <label class="label" for="dominance-granularity">Granularity</label>
        <select id="dominance-granularity" v-model="granularity" class="input">
          <option value="daily">Harian</option>
          <option value="weekly">Mingguan</option>
          <option value="monthly">Bulanan</option>
        </select>
      </div>
    </FilterBar>

    <p
      v-if="!filterValid"
      class="status-warning"
    >
      Rentang tanggal tidak valid. Pastikan format YYYY-MM-DD dan tanggal akhir
      tidak lebih awal dari tanggal mulai.
    </p>

    <DashboardGrid :columns="1">
      <DominanceRatioChart
        :model="dominanceQuery.chartModel.value"
        :loading="dominanceQuery.isPending.value"
        :fetching="dominanceQuery.isFetching.value"
        :error="dominanceQuery.errorMessage.value"
        @retry="dominanceQuery.refetch()"
      />
      <NetValueRankingChart
        :model="rankingQuery.chartModel.value"
        :loading="rankingQuery.isPending.value"
        :fetching="rankingQuery.isFetching.value"
        :error="rankingQuery.errorMessage.value"
        @retry="rankingQuery.refetch()"
        @select-stock="openStockDetail"
      />
    </DashboardGrid>
  </div>
</template>

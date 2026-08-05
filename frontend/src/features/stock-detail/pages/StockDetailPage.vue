<script setup lang="ts">
import { computed, ref, watch } from "vue";
import { useRoute, useRouter } from "vue-router";
import PageHeader from "@/shared/components/layout/PageHeader.vue";
import DashboardGrid from "@/shared/components/layout/DashboardGrid.vue";
import FilterBar from "@/shared/components/filters/FilterBar.vue";
import DateRangeFilter from "@/shared/components/filters/DateRangeFilter.vue";
import StockCodeSelect from "@/shared/components/filters/StockCodeSelect.vue";
import { useDebouncedRef } from "@/shared/composables/useDebouncedRef";
import NetValueTrendChart from "@/features/stock-detail/components/NetValueTrendChart.vue";
import InvestorNetFlowChart from "@/features/stock-detail/components/InvestorNetFlowChart.vue";
import ForeignGrossFlowChart from "@/features/stock-detail/components/ForeignGrossFlowChart.vue";
import CumulativeNetValueChart from "@/features/stock-detail/components/CumulativeNetValueChart.vue";
import { useNetValueTrendQuery } from "@/features/stock-detail/composables/useNetValueTrendQuery";
import { useInvestorNetFlowQuery } from "@/features/stock-detail/composables/useInvestorNetFlowQuery";
import { useForeignGrossFlowQuery } from "@/features/stock-detail/composables/useForeignGrossFlowQuery";
import { useCumulativeNetValueQuery } from "@/features/stock-detail/composables/useCumulativeNetValueQuery";
import {
  defaultEndDate,
  defaultStartDate,
} from "@/shared/utils/date-defaults";
import { isValidDateRange } from "@/shared/formatters/date";
import {
  isValidStockCode,
  normalizeStockCode,
} from "@/shared/utils/stock-code";

const route = useRoute();
const router = useRouter();

function readQueryString(value: unknown, fallback: string): string {
  return typeof value === "string" && value.length > 0 ? value : fallback;
}

const stockCode = ref(
  normalizeStockCode(
    typeof route.params.stockCode === "string" ? route.params.stockCode : "BBCA",
  ),
);
const debouncedStockCode = useDebouncedRef(stockCode, 300);
const startDate = ref(readQueryString(route.query.start_date, defaultStartDate()));
const endDate = ref(readQueryString(route.query.end_date, defaultEndDate()));

watch(
  () => route.params.stockCode,
  (value) => {
    if (typeof value === "string" && value.length > 0) {
      stockCode.value = normalizeStockCode(value);
    }
  },
);

watch(debouncedStockCode, () => {
  const code = normalizeStockCode(debouncedStockCode.value);
  if (!isValidStockCode(code)) return;

  void router.replace({
    name: "stock-detail",
    params: { stockCode: code },
    query: {
      start_date: startDate.value,
      end_date: endDate.value,
    },
  });
});

watch([startDate, endDate], () => {
  void router.replace({
    name: "stock-detail",
    params: route.params,
    query: {
      start_date: startDate.value,
      end_date: endDate.value,
    },
  });
});

const sharedParams = computed(() => ({
  stock_code: normalizeStockCode(debouncedStockCode.value),
  start_date: startDate.value,
  end_date: endDate.value,
}));

const trendQuery = useNetValueTrendQuery(sharedParams);
const flowQuery = useInvestorNetFlowQuery(sharedParams);
const foreignGrossQuery = useForeignGrossFlowQuery(sharedParams);
const cumulativeQuery = useCumulativeNetValueQuery(sharedParams);

const filterValid = computed(
  () =>
    isValidStockCode(stockCode.value) &&
    isValidDateRange(startDate.value, endDate.value),
);
</script>

<template>
  <div class="space-y-4">
    <PageHeader
      :title="`Detail Saham ${stockCode || '—'}`"
      subtitle="Tren net value dan perbandingan foreign vs domestic net flow untuk emiten terpilih."
    >
      <RouterLink to="/market" class="link text-sm">
        ← Kembali ke Market Overview
      </RouterLink>
    </PageHeader>

    <FilterBar title="Filter saham & periode">
      <StockCodeSelect v-model="stockCode" class="w-full sm:w-40" />
      <DateRangeFilter
        v-model:start-date="startDate"
        v-model:end-date="endDate"
        id-prefix="stock"
        class="flex-1"
      />
    </FilterBar>

    <p
      v-if="!filterValid"
      class="status-warning"
    >
      Kode saham atau rentang tanggal tidak valid. Kode maks. 10 karakter
      alfanumerik; tanggal akhir tidak boleh lebih awal dari tanggal mulai.
    </p>

    <DashboardGrid>
      <NetValueTrendChart
        :model="trendQuery.chartModel.value"
        :loading="trendQuery.isPending.value"
        :fetching="trendQuery.isFetching.value"
        :error="trendQuery.errorMessage.value"
        @retry="trendQuery.refetch()"
      />
      <InvestorNetFlowChart
        :model="flowQuery.chartModel.value"
        :loading="flowQuery.isPending.value"
        :fetching="flowQuery.isFetching.value"
        :error="flowQuery.errorMessage.value"
        @retry="flowQuery.refetch()"
      />
      <ForeignGrossFlowChart
        :model="foreignGrossQuery.chartModel.value"
        :loading="foreignGrossQuery.isPending.value"
        :fetching="foreignGrossQuery.isFetching.value"
        :error="foreignGrossQuery.errorMessage.value"
        @retry="foreignGrossQuery.refetch()"
      />
      <CumulativeNetValueChart
        :model="cumulativeQuery.chartModel.value"
        :loading="cumulativeQuery.isPending.value"
        :fetching="cumulativeQuery.isFetching.value"
        :error="cumulativeQuery.errorMessage.value"
        @retry="cumulativeQuery.refetch()"
      />
    </DashboardGrid>
  </div>
</template>

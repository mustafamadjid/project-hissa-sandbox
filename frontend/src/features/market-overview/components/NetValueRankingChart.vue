<script setup lang="ts">
import { computed, shallowRef, watch } from "vue";
import BaseChart, {
  type ChartOption,
} from "@/shared/components/charts/BaseChart.vue";
import ChartCard from "@/shared/components/charts/ChartCard.vue";
import {
  mapNetValueRankingToOption,
} from "@/features/market-overview/mappers/mapNetValueRankingToChartModel";
import type { NetValueRankingChartModel } from "@/features/market-overview/types/net-value-ranking.types";
import { formatApiDate } from "@/shared/formatters/date";

const props = defineProps<{
  model: NetValueRankingChartModel | null;
  loading?: boolean;
  fetching?: boolean;
  error?: string | null;
}>();

const emit = defineEmits<{
  retry: [];
  selectStock: [stockCode: string];
}>();

const DESCRIPTION =
  "Peringkat emiten dengan net value tertinggi (akumulasi, ke kanan) dan terendah (distribusi, ke kiri). Nol di tengah. Klik bar untuk buka detail saham.";

const option = shallowRef<ChartOption | null>(null);

watch(
  () => props.model,
  (model) => {
    option.value = model ? mapNetValueRankingToOption(model) : null;
  },
  { immediate: true },
);

const empty = computed(
  () => !props.loading && !props.error && (props.model?.bars.length ?? 0) === 0,
);

const errorMessage = computed(() => props.error ?? null);

const periodLabel = computed(() => {
  if (!props.model) return null;
  return `${formatApiDate(props.model.period.start_date)} – ${formatApiDate(props.model.period.end_date)}`;
});

function onChartClick(params: unknown): void {
  if (!props.model) return;
  const payload = params as { dataIndex?: number };
  const index = payload.dataIndex;
  if (index === undefined) return;
  const bar = props.model.bars[index];
  if (bar) {
    emit("selectStock", bar.stockCode);
  }
}
</script>

<template>
  <ChartCard
    title="Top Akumulasi & Distribusi"
    :description="DESCRIPTION"
    :fetching="fetching && !loading"
  >
    <BaseChart
      :option="option"
      :loading="loading"
      :error="errorMessage"
      :empty="empty"
      height="32rem"
      aria-label="Grafik peringkat akumulasi dan distribusi net value"
      @retry="emit('retry')"
      @chart-click="onChartClick"
    />
    <template v-if="periodLabel" #footer>
      <p class="text-xs text-gray-500 dark:text-gray-400">
        Periode: {{ periodLabel }} · Limit {{ model?.limit ?? "—" }} per klasifikasi
      </p>
    </template>
  </ChartCard>
</template>

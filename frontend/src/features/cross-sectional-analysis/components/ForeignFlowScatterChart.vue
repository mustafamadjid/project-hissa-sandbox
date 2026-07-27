<script setup lang="ts">
import { computed, shallowRef, watch } from "vue";
import BaseChart, { type ChartOption } from "@/shared/components/charts/BaseChart.vue";
import ChartCard from "@/shared/components/charts/ChartCard.vue";
import { mapForeignFlowScatterToOption } from "@/features/cross-sectional-analysis/mappers/mapForeignFlowScatterToChartModel";
import type { ForeignFlowScatterChartModel } from "@/features/cross-sectional-analysis/types/foreign-flow-scatter.types";
import { formatApiDate } from "@/shared/formatters/date";

const props = defineProps<{ model: ForeignFlowScatterChartModel | null; loading?: boolean; fetching?: boolean; error?: string | null }>();
const emit = defineEmits<{ retry: []; selectStock: [stockCode: string] }>();
const option = shallowRef<ChartOption | null>(null);
const description = "Setiap titik adalah saham. Kanan/kiri berarti beli/jual asing; atas/bawah berarti akumulasi/distribusi. Garis putus-putus menandai nol.";

watch(() => props.model, (model) => { option.value = model ? mapForeignFlowScatterToOption(model) : null; }, { immediate: true });
const empty = computed(() => !props.loading && !props.error && (props.model?.points.length ?? 0) === 0);
const periodLabel = computed(() => props.model ? `${formatApiDate(props.model.period.start_date)} – ${formatApiDate(props.model.period.end_date)}` : null);
function onChartClick(params: unknown): void {
  const index = (params as { dataIndex?: number }).dataIndex;
  const point = index === undefined ? undefined : props.model?.points[index];
  if (point) emit("selectStock", point.stockCode);
}
</script>

<template>
  <ChartCard title="Foreign Flow vs Net Value" :description="description" :fetching="fetching && !loading">
    <BaseChart :option="option" :loading="loading" :error="error ?? null" :empty="empty" height="32rem" aria-label="Scatter foreign net flow versus net value" @retry="emit('retry')" @chart-click="onChartClick" />
    <template v-if="periodLabel" #footer>
      <p class="text-xs text-gray-500 dark:text-gray-400">Periode: {{ periodLabel }} · Agregasi: {{ model?.aggregation }}</p>
    </template>
  </ChartCard>
</template>

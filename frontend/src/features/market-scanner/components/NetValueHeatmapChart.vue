<script setup lang="ts">
import { computed, shallowRef, watch } from "vue";
import BaseChart, { type ChartOption } from "@/shared/components/charts/BaseChart.vue";
import ChartCard from "@/shared/components/charts/ChartCard.vue";
import { mapNetValueHeatmapToOption } from "@/features/market-scanner/mappers/mapNetValueHeatmapToChartModel";
import type { NetValueHeatmapChartModel } from "@/features/market-scanner/types/net-value-heatmap.types";
const props = defineProps<{ model: NetValueHeatmapChartModel | null; loading?: boolean; fetching?: boolean; error?: string | null }>();
const emit = defineEmits<{ retry: []; selectStock: [stockCode: string] }>();
const option = shallowRef<ChartOption | null>(null);
watch(() => props.model, (model) => { option.value = model ? mapNetValueHeatmapToOption(model) : null; }, { immediate: true });
const empty = computed(() => !props.loading && !props.error && (props.model?.data.length ?? 0) === 0);
function onChartClick(params: unknown): void { const data = (params as { data?: [number, number, number, number] }).data; const stock = data ? props.model?.stocks[data[1]] : undefined; if (stock) emit("selectStock", stock); }
</script>
<template>
  <ChartCard title="Heatmap Net Value" description="Peta panas net value per saham dan tanggal. Sel kosong berarti data tidak tersedia." :fetching="fetching && !loading">
    <BaseChart :option="option" :loading="loading" :error="error ?? null" :empty="empty" height="28rem" aria-label="Heatmap net value pasar" @retry="emit('retry')" @chart-click="onChartClick" />
  </ChartCard>
</template>

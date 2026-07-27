<script setup lang="ts">
import { computed, shallowRef, watch } from "vue";
import BaseChart, { type ChartOption } from "@/shared/components/charts/BaseChart.vue";
import ChartCard from "@/shared/components/charts/ChartCard.vue";
import { mapForeignGrossFlowToOption } from "@/features/stock-detail/mappers/mapForeignGrossFlowToChartModel";
import type { ForeignGrossFlowChartModel } from "@/features/stock-detail/types/foreign-gross-flow.types";
const props = defineProps<{ model: ForeignGrossFlowChartModel | null; loading?: boolean; fetching?: boolean; error?: string | null }>();
const emit = defineEmits<{ retry: [] }>();
const option = shallowRef<ChartOption | null>(null);
watch(() => props.model, (model) => { option.value = model ? mapForeignGrossFlowToOption(model) : null; }, { immediate: true });
const empty = computed(() => !props.loading && !props.error && (props.model?.labels.length ?? 0) === 0);
</script>
<template><ChartCard title="Foreign Buy vs Sell" description="Perbandingan gross foreign buy dan sell harian; net flow tampil di tooltip." :fetching="fetching && !loading"><BaseChart :option="option" :loading="loading" :error="error ?? null" :empty="empty" height="22rem" :aria-label="`Grafik foreign buy vs sell ${model?.stockCode ?? ''}`" @retry="emit('retry')" /></ChartCard></template>

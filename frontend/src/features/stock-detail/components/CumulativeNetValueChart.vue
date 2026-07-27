<script setup lang="ts">
import { computed, shallowRef, watch } from "vue";
import BaseChart, { type ChartOption } from "@/shared/components/charts/BaseChart.vue";
import ChartCard from "@/shared/components/charts/ChartCard.vue";
import { mapCumulativeNetValueToOption } from "@/features/stock-detail/mappers/mapCumulativeNetValueToChartModel";
import type { CumulativeNetValueChartModel } from "@/features/stock-detail/types/cumulative-net-value.types";
const props = defineProps<{ model: CumulativeNetValueChartModel | null; loading?: boolean; fetching?: boolean; error?: string | null }>();
const emit = defineEmits<{ retry: [] }>();
const option = shallowRef<ChartOption | null>(null);
watch(() => props.model, (model) => { option.value = model ? mapCumulativeNetValueToOption(model) : null; }, { immediate: true });
const empty = computed(() => !props.loading && !props.error && (props.model?.labels.length ?? 0) === 0);
</script>
<template><ChartCard title="Cumulative Net Value" description="Net value kumulatif direset dari awal periode terpilih; nilai harian hanya tampil di tooltip." :fetching="fetching && !loading"><BaseChart :option="option" :loading="loading" :error="error ?? null" :empty="empty" height="22rem" :aria-label="`Grafik cumulative net value ${model?.stockCode ?? ''}`" @retry="emit('retry')" /></ChartCard></template>

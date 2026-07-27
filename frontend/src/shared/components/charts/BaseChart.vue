<script setup lang="ts">
import { computed, toRef } from "vue";
import VChart from "vue-echarts";
import type { ComposeOption } from "echarts/core";
import type {
  BarSeriesOption,
  HeatmapSeriesOption,
  LineSeriesOption,
  ScatterSeriesOption,
} from "echarts/charts";
import type {
  DataZoomComponentOption,
  GridComponentOption,
  LegendComponentOption,
  MarkLineComponentOption,
  TooltipComponentOption,
  VisualMapComponentOption,
} from "echarts/components";
import AppSkeleton from "@/shared/components/feedback/AppSkeleton.vue";
import EmptyState from "@/shared/components/feedback/EmptyState.vue";
import ErrorState from "@/shared/components/feedback/ErrorState.vue";
import { registerEcharts } from "@/shared/utils/echarts";

registerEcharts();

export type ChartOption = ComposeOption<
  | BarSeriesOption
  | HeatmapSeriesOption
  | LineSeriesOption
  | ScatterSeriesOption
  | TooltipComponentOption
  | GridComponentOption
  | LegendComponentOption
  | MarkLineComponentOption
  | DataZoomComponentOption
  | VisualMapComponentOption
>;

const props = withDefaults(
  defineProps<{
    option: ChartOption | null;
    loading?: boolean;
    error?: string | null;
    empty?: boolean;
    emptyMessage?: string;
    height?: string;
    ariaLabel?: string;
  }>(),
  {
    loading: false,
    error: null,
    empty: false,
    emptyMessage: "Tidak ada data untuk filter yang dipilih.",
    height: "20rem",
    ariaLabel: "Grafik",
  },
);

const emit = defineEmits<{
  retry: [];
  chartClick: [params: unknown];
}>();

const optionRef = toRef(props, "option");
const hasOption = computed(() => optionRef.value !== null);
</script>

<template>
  <div class="relative w-full" :style="{ minHeight: height }">
    <AppSkeleton v-if="loading && !hasOption" :height="height" />

    <ErrorState
      v-else-if="error"
      :message="error"
      @retry="emit('retry')"
    />

    <EmptyState
      v-else-if="empty || !hasOption"
      :message="emptyMessage"
    />

    <div v-else class="relative w-full" :style="{ height }">
      <div
        v-if="loading"
        class="pointer-events-none absolute inset-0 z-10 flex items-start justify-end p-2"
      >
        <span
          class="rounded-full bg-white/95 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-primary-700 shadow-subtle dark:bg-gray-900/95 dark:text-primary-300"
        >
          Memuat
        </span>
      </div>
      <VChart
        class="h-full w-full"
        :option="option!"
        autoresize
        :aria-label="ariaLabel"
        role="img"
        @click="emit('chartClick', $event)"
      />
    </div>
  </div>
</template>

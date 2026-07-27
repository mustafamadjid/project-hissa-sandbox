<script setup lang="ts">
import { computed, shallowRef, watch } from "vue";
import BaseChart, {
  type ChartOption,
} from "@/shared/components/charts/BaseChart.vue";
import ChartCard from "@/shared/components/charts/ChartCard.vue";
import { mapInvestorNetFlowToOption } from "@/features/stock-detail/mappers/mapInvestorNetFlowToChartModel";
import type { InvestorNetFlowChartModel } from "@/features/stock-detail/types/investor-net-flow.types";

const props = defineProps<{
  model: InvestorNetFlowChartModel | null;
  loading?: boolean;
  fetching?: boolean;
  error?: string | null;
}>();

const emit = defineEmits<{
  retry: [];
}>();

const DESCRIPTION =
  "Perbandingan net flow investor asing (foreign) dan domestik per hari. Dua garis dengan legend; klik legend untuk toggle series. Garis nol sebagai acuan. Tooltip memakai shared axis pointer.";

const option = shallowRef<ChartOption | null>(null);

watch(
  () => props.model,
  (model) => {
    option.value = model ? mapInvestorNetFlowToOption(model) : null;
  },
  { immediate: true },
);

const empty = computed(
  () =>
    !props.loading &&
    !props.error &&
    (props.model?.labels.length ?? 0) === 0,
);

const errorMessage = computed(() => props.error ?? null);
</script>

<template>
  <ChartCard
    title="Foreign vs Domestic Net Flow"
    :description="DESCRIPTION"
    :fetching="fetching && !loading"
  >
    <BaseChart
      :option="option"
      :loading="loading"
      :error="errorMessage"
      :empty="empty"
      height="22rem"
      :aria-label="`Grafik foreign vs domestic net flow ${model?.stockCode ?? ''}`"
      @retry="emit('retry')"
    />
    <template v-if="model" #footer>
      <p class="text-xs text-gray-500 dark:text-gray-400">
        {{ model.stockCode }} · Granularitas harian · Unit IDR
      </p>
    </template>
  </ChartCard>
</template>

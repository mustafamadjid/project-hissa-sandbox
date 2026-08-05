<script setup lang="ts">
import { computed, shallowRef, watch } from "vue";
import BaseChart, {
  type ChartOption,
} from "@/shared/components/charts/BaseChart.vue";
import ChartCard from "@/shared/components/charts/ChartCard.vue";
import { mapDominanceRatioToOption } from "@/features/market-overview/mappers/mapDominanceRatioToChartModel";
import type { DominanceRatioChartModel } from "@/features/market-overview/types/dominance-ratio.types";

const props = defineProps<{
  model: DominanceRatioChartModel | null;
  loading?: boolean;
  fetching?: boolean;
  error?: string | null;
}>();

const emit = defineEmits<{
  retry: [];
}>();

const DESCRIPTION =
  "Komposisi proporsi investor institusi, retail, dan campuran (total visual 100%). Basis: nilai transaksi. Hover bar untuk detail angka.";

const option = shallowRef<ChartOption | null>(null);

watch(
  () => props.model,
  (model) => {
    option.value = model ? mapDominanceRatioToOption(model) : null;
  },
  { immediate: true },
);

const empty = computed(
  () =>
    !props.loading && !props.error && (props.model?.segments.length ?? 0) === 0,
);

const errorMessage = computed(() => props.error ?? null);

const showQualityWarning = computed(
  () => props.model?.hasQualityWarning === true,
);
</script>

<template>
  <ChartCard
    title="Dominance Ratio"
    :description="DESCRIPTION"
    :fetching="fetching && !loading"
  >
    <div v-if="showQualityWarning" class="status-warning mb-3" role="status">
      Beberapa baris memiliki total rasio di luar toleransi 99.5-100.5%. Data
      ditampilkan apa adanya tanpa normalisasi ulang.
    </div>
    <BaseChart
      :option="option"
      :loading="loading"
      :error="errorMessage"
      :empty="empty"
      height="1000rem"
      aria-label="Grafik dominance ratio institusi retail campuran"
      @retry="emit('retry')"
    />
    <template v-if="model" #footer>
      <p class="text-xs text-gray-500 dark:text-gray-400">
        Basis: nilai transaksi · Unit: persen
      </p>
    </template>
  </ChartCard>
</template>

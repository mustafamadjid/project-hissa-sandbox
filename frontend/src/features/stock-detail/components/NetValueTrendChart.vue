<script setup lang="ts">
import { computed, shallowRef, watch } from "vue";
import BaseChart, {
  type ChartOption,
} from "@/shared/components/charts/BaseChart.vue";
import ChartCard from "@/shared/components/charts/ChartCard.vue";
import UpdatedAtLabel from "@/shared/components/data-display/UpdatedAtLabel.vue";
import { mapNetValueTrendToOption } from "@/features/stock-detail/mappers/mapNetValueTrendToChartModel";
import type { NetValueTrendChartModel } from "@/features/stock-detail/types/net-value-trend.types";
import { formatApiDate } from "@/shared/formatters/date";

const props = defineProps<{
  model: NetValueTrendChartModel | null;
  loading?: boolean;
  fetching?: boolean;
  error?: string | null;
}>();

const emit = defineEmits<{
  retry: [];
}>();

const DESCRIPTION =
  "Tren net value harian emiten. Sumbu X = tanggal, sumbu Y = net value (IDR). Garis putus-putus di nol. Positif = akumulasi, negatif = distribusi. Tooltip menampilkan nilai penuh dan klasifikasi.";

const option = shallowRef<ChartOption | null>(null);

watch(
  () => props.model,
  (model) => {
    option.value = model ? mapNetValueTrendToOption(model) : null;
  },
  { immediate: true },
);

const empty = computed(
  () =>
    !props.loading && !props.error && (props.model?.values.length ?? 0) === 0,
);

const errorMessage = computed(() => props.error ?? null);

const updatedAt = computed(() => props.model?.updatedAt ?? null);

const periodLabel = computed(() => {
  if (!props.model) return null;
  return `${formatApiDate(props.model.period.start_date)} – ${formatApiDate(props.model.period.end_date)}`;
});
</script>

<template>
  <ChartCard
    title="Tren Net Value"
    :description="DESCRIPTION"
    :fetching="fetching && !loading"
  >
    <BaseChart
      :option="option"
      :loading="loading"
      :error="errorMessage"
      :empty="empty"
      height="22rem"
      :aria-label="`Grafik tren net value ${model?.stockCode ?? ''}`"
      @retry="emit('retry')"
    />
    <template v-if="model" #footer>
      <div class="flex flex-wrap items-center justify-between gap-2">
        <p class="text-xs text-gray-500 dark:text-gray-400">
          {{ model.stockCode }} · {{ periodLabel }}
        </p>
        <UpdatedAtLabel :value="updatedAt" />
      </div>
    </template>
  </ChartCard>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { formatPercentRatio } from "@/shared/formatters/percentage";

const props = defineProps<{
  value: number;
}>();

const formatted = computed(() => formatPercentRatio(props.value));
</script>

<template>
  <span class="font-medium tabular-nums text-gray-800 dark:text-gray-100">
    {{ formatted }}
  </span>
</template>

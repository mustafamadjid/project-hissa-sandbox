<script setup lang="ts">
import { computed } from "vue";
import { formatIdr } from "@/shared/formatters/currency";

const props = defineProps<{
  value: number;
}>();

const formatted = computed(() => formatIdr(props.value));
const toneClass = computed(() => {
  if (props.value > 0) return "text-halal-700 dark:text-halal-400";
  if (props.value < 0) return "text-non-halal-700 dark:text-non-halal-400";
  return "text-gray-700 dark:text-gray-300";
});
</script>

<template>
  <span class="font-medium tabular-nums" :class="toneClass">{{ formatted }}</span>
</template>

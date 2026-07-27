<script setup lang="ts">
import { computed } from "vue";

const props = defineProps<{
  classification: string;
}>();

const label = computed(() => {
  const value = props.classification.toLowerCase();
  if (value === "accumulation" || value === "akumulasi") return "Akumulasi";
  if (value === "distribution" || value === "distribusi") return "Distribusi";
  return props.classification;
});

const toneClass = computed(() => {
  const value = props.classification.toLowerCase();
  if (value === "accumulation" || value === "akumulasi") {
    return "bg-halal-100 text-halal-700 dark:bg-halal-900/30 dark:text-halal-400";
  }
  if (value === "distribution" || value === "distribusi") {
    return "bg-non-halal-100 text-non-halal-700 dark:bg-non-halal-900/30 dark:text-non-halal-400";
  }
  return "bg-gray-100 text-gray-700 ring-1 ring-inset ring-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-700";
});
</script>

<template>
  <span
    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wider"
    :class="toneClass"
  >
    {{ label }}
  </span>
</template>

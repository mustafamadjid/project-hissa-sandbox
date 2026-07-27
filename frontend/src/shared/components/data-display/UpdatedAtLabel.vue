<script setup lang="ts">
import { computed } from "vue";
import { formatApiDate } from "@/shared/formatters/date";

const props = defineProps<{
  value?: string | null;
}>();

const label = computed(() => {
  if (!props.value) return null;
  return formatApiDate(props.value);
});
</script>

<template>
  <p v-if="label" class="text-xs text-gray-500 dark:text-gray-400">
    Diperbarui: {{ label }}
  </p>
</template>

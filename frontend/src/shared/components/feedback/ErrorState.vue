<script setup lang="ts">
import RetryButton from "./RetryButton.vue";

withDefaults(
  defineProps<{
    title?: string;
    message: string;
  }>(),
  {
    title: "Gagal memuat data",
  },
);

defineEmits<{
  retry: [];
}>();
</script>

<template>
  <div
    class="flex min-h-72 flex-col items-center justify-center gap-3 rounded-xl border border-red-200 bg-red-50 p-6 text-center dark:border-red-900/50 dark:bg-red-950/20"
    role="alert"
  >
    <h3 class="text-base font-semibold text-non-halal-700 dark:text-non-halal-400">
      {{ title }}
    </h3>
    <p class="max-w-md text-sm text-gray-600 dark:text-gray-300">
      {{ message }}
    </p>
    <RetryButton @retry="$emit('retry')" />
  </div>
</template>

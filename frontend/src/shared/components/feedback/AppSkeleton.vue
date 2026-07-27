<script setup lang="ts">
withDefaults(
  defineProps<{
    height?: string;
    className?: string;
  }>(),
  {
    height: "16rem",
    className: "",
  },
);
</script>

<template>
  <div
    class="animate-pulse rounded-lg bg-gray-100 dark:bg-gray-800"
    :class="className"
    :style="{ height }"
    role="status"
    aria-label="Memuat data"
  />
</template>

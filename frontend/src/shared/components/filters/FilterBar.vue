<script setup lang="ts">
defineProps<{
  title?: string;
}>();
</script>

<template>
  <div
    class="panel flex flex-col gap-3 bg-surface-muted p-4 dark:bg-gray-900"
    role="search"
    :aria-label="title ?? 'Filter dashboard'"
  >
    <div v-if="title" class="text-sm font-semibold text-gray-700 dark:text-gray-200">
      {{ title }}
    </div>
    <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:gap-4">
      <slot />
    </div>
  </div>
</template>

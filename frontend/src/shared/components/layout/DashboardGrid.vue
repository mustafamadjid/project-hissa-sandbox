<script setup lang="ts">
withDefaults(
  defineProps<{
    columns?: 1 | 2;
  }>(),
  {
    columns: 2,
  },
);
</script>

<template>
  <div
    class="grid gap-4"
    :class="columns === 2 ? 'grid-cols-1 lg:grid-cols-2' : 'grid-cols-1'"
  >
    <slot />
  </div>
</template>

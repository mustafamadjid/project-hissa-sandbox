<script setup lang="ts">
defineProps<{
  title?: string;
}>();
</script>

<template>
  <section class="space-y-4">
    <h2
      v-if="title"
      class="text-lg font-semibold text-gray-900 dark:text-gray-100"
    >
      {{ title }}
    </h2>
    <slot />
  </section>
</template>

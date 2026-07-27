<script setup lang="ts">
defineProps<{
  title: string;
  subtitle?: string;
}>();
</script>

<template>
  <header class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
    <div>
      <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 lg:text-3xl">
        {{ title }}
      </h1>
      <p v-if="subtitle" class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        {{ subtitle }}
      </p>
    </div>
    <div v-if="$slots.default" class="shrink-0"><slot /></div>
  </header>
</template>

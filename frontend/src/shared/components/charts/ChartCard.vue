<script setup lang="ts">
defineProps<{
  title: string;
  description: string;
  fetching?: boolean;
}>();
</script>

<template>
  <article class="panel flex h-full flex-col overflow-hidden">
    <header class="panel-header">
      <div class="min-w-0 space-y-1.5">
        <div class="flex items-start justify-between gap-3">
          <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">
            {{ title }}
          </h3>
          <span
            v-if="fetching"
            class="shrink-0 rounded-full bg-primary-50 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-primary-700 dark:bg-primary-900/30 dark:text-primary-300"
          >
            Memperbarui
          </span>
        </div>
        <p class="text-xs leading-relaxed text-gray-500 dark:text-gray-400">
          {{ description }}
        </p>
      </div>
    </header>
    <div class="min-h-0 flex-1 p-4">
      <slot />
    </div>
    <footer v-if="$slots.footer" class="border-t border-gray-100 px-4 py-3 dark:border-gray-800">
      <slot name="footer" />
    </footer>
  </article>
</template>

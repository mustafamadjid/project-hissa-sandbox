<script setup lang="ts">
import { computed } from "vue";
import { useRoute } from "vue-router";

const emit = defineEmits<{ close: [] }>();
const route = useRoute();
const links = [
  { to: "/market", label: "Market Overview", icon: "▦", name: "market-overview" },
  { to: "/market/scanner", label: "Market Scanner", icon: "⌘", name: "market-scanner" },
  { to: "/market/analysis/foreign-flow", label: "Foreign Flow", icon: "◎", name: "foreign-flow-analysis" },
  { to: "/stocks/BBCA", label: "Detail Saham", icon: "↗", name: "stock-detail" },
] as const;
const activeName = computed(() => route.name);
</script>

<template>
  <aside class="flex h-full w-64 flex-col border-r border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <RouterLink to="/market" class="flex h-16 items-center gap-3 border-b border-gray-200 px-5 dark:border-gray-800" @click="emit('close')">
      <span class="grid size-9 place-items-center rounded-xl bg-primary-600 text-sm font-bold text-white">H</span>
      <span><strong class="block text-base text-gray-900 dark:text-white">HISSA</strong><small class="block text-[11px] text-gray-500 dark:text-gray-400">Market analytics syariah</small></span>
    </RouterLink>
    <nav class="flex-1 p-3" aria-label="Navigasi utama">
      <p class="mb-2 px-3 text-[11px] font-medium uppercase tracking-wide text-gray-400">Menu utama</p>
      <div class="space-y-1">
        <RouterLink v-for="link in links" :key="link.to" :to="link.to" class="flex min-h-11 items-center gap-3 rounded-lg border px-3 py-2 text-sm font-medium transition-[color,background-color,border-color] duration-150" :class="activeName === link.name ? 'border-gray-200 bg-gray-50 font-semibold text-gray-900 shadow-subtle dark:border-gray-700 dark:bg-gray-800 dark:text-white' : 'border-transparent text-gray-600 hover:bg-surface-hover hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white'" @click="emit('close')"><span class="text-primary-600" aria-hidden="true">{{ link.icon }}</span>{{ link.label }}</RouterLink>
      </div>
    </nav>
    <div class="border-t border-gray-200 p-4 dark:border-gray-800"><p class="text-xs text-gray-500 dark:text-gray-400">Data analitik. Bukan rekomendasi investasi.</p></div>
  </aside>
</template>

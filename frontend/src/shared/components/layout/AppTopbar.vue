<script setup lang="ts">
defineEmits<{ openMenu: []; toggleTheme: [] }>();

defineProps<{ darkMode: boolean }>();
</script>

<template>
  <header class="flex h-16 items-center justify-between gap-3 border-b border-gray-200 px-4 md:px-5 dark:border-gray-800">
    <div class="flex min-w-0 flex-1 items-center gap-3">
      <button type="button" class="icon-button lg:hidden" aria-label="Buka menu" @click="$emit('openMenu')">☰</button>
      <label class="flex h-10 w-full max-w-sm items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 shadow-subtle focus-within:border-primary-500 focus-within:ring-2 focus-within:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900">
        <span class="text-gray-400" aria-hidden="true">⌕</span>
        <span class="sr-only">Cari saham</span>
        <input class="min-w-0 flex-1 bg-transparent text-sm text-gray-900 outline-none placeholder:text-gray-400 dark:text-gray-100" placeholder="Cari kode saham" type="search" autocomplete="off">
      </label>
    </div>
    <div class="flex items-center gap-2">
      <button type="button" class="icon-button" :aria-label="darkMode ? 'Aktifkan mode terang' : 'Aktifkan mode gelap'" @click="$emit('toggleTheme')">{{ darkMode ? '☾' : '☼' }}</button>
      <button type="button" class="icon-button" aria-label="Notifikasi">•</button>
    </div>
  </header>
</template>

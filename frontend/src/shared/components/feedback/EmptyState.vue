<script setup lang="ts">
withDefaults(
  defineProps<{
    title?: string;
    message?: string;
  }>(),
  {
    title: "Tidak ada data",
    message: "Tidak ada data untuk filter yang dipilih. Coba ubah periode atau kode saham.",
  },
);
</script>

<template>
  <div
    class="flex min-h-72 flex-col items-center justify-center gap-2 rounded-xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center dark:border-gray-700 dark:bg-gray-800/40"
    role="status"
  >
    <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">
      {{ title }}
    </h3>
    <p class="max-w-md text-sm text-gray-600 dark:text-gray-400">
      {{ message }}
    </p>
    <slot />
  </div>
</template>

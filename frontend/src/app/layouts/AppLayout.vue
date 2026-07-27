<script setup lang="ts">
import { ref, watch } from "vue";
import AppSidebar from "@/shared/components/layout/AppSidebar.vue";
import AppTopbar from "@/shared/components/layout/AppTopbar.vue";
import MarketTicker from "@/shared/components/layout/MarketTicker.vue";

const showMobileSidebar = ref(false);
const darkMode = ref(false);

watch([darkMode], () => {
  document.documentElement.classList.toggle("dark", darkMode.value);
}, { immediate: true });
</script>

<template>
  <div class="min-h-screen bg-app p-3 md:p-5 dark:bg-gray-950">
    <div class="app-shell min-h-[calc(100vh-2.5rem)]">
      <div class="flex h-full">
        <Teleport to="body">
          <div v-if="showMobileSidebar" class="fixed inset-0 z-50 flex">
            <div class="absolute inset-0 bg-black/30" @click="showMobileSidebar = false" @keydown.escape="showMobileSidebar = false" />
            <div class="relative z-10 animate-slide-in-left"><AppSidebar @close="showMobileSidebar = false" /></div>
          </div>
        </Teleport>
        <AppSidebar class="hidden lg:flex lg:shrink-0" @close="() => {}" />
        <div class="flex min-w-0 flex-1 flex-col">
          <AppTopbar :dark-mode="darkMode" @open-menu="showMobileSidebar = true" @toggle-theme="darkMode = !darkMode" />
          <MarketTicker />
          <main class="flex-1 p-4 md:p-5"><RouterView /></main>
        </div>
      </div>
    </div>
  </div>
</template>

<style>
@keyframes slideInLeft { from { transform: translateX(-100%); } to { transform: translateX(0); } }
.animate-slide-in-left { animation: slideInLeft 200ms ease-out; }
@media (prefers-reduced-motion: reduce) { .animate-slide-in-left { animation: none; } }
</style>

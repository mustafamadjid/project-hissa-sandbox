<script setup lang="ts">
import { computed, ref } from "vue";
import { useQuery } from "@tanstack/vue-query";
import { httpClient } from "@/shared/api/http-client";
import { normalizeStockCode } from "@/shared/utils/stock-code";

const model = defineModel<string>({ required: true });

defineProps<{
  id?: string;
  label?: string;
  placeholder?: string;
}>();

const isOpen = ref(false);
const highlightedIndex = ref(-1);

const { data: stockCodesData } = useQuery({
  queryKey: ["stock-codes"],
  queryFn: async () => {
    const response = await httpClient.get<{ items: string[] }>("/market/stock-codes");
    return response.data.items;
  },
  staleTime: 10 * 60 * 1000,
});

const stockCodes = computed(() => stockCodesData.value ?? []);

const filteredCodes = computed(() => {
  const query = model.value.toUpperCase().trim();
  if (!query) return stockCodes.value;
  return stockCodes.value.filter((code) => code.includes(query));
});

function onInput(event: Event): void {
  const target = event.target as HTMLInputElement;
  model.value = normalizeStockCode(target.value);
  highlightedIndex.value = -1;
  isOpen.value = true;
}

function selectCode(code: string): void {
  model.value = code;
  isOpen.value = false;
  highlightedIndex.value = -1;
}

function onFocus(): void {
  isOpen.value = true;
}

function onBlur(): void {
  setTimeout(() => {
    isOpen.value = false;
  }, 150);
}

function onKeydown(event: KeyboardEvent): void {
  if (event.key === "Escape") {
    isOpen.value = false;
    highlightedIndex.value = -1;
    return;
  }

  if (!isOpen.value) {
    if (event.key === "ArrowDown" || event.key === "ArrowUp") {
      isOpen.value = true;
      event.preventDefault();
    }
    return;
  }

  if (event.key === "ArrowDown") {
    event.preventDefault();
    highlightedIndex.value = Math.min(
      highlightedIndex.value + 1,
      filteredCodes.value.length - 1,
    );
    return;
  }

  if (event.key === "ArrowUp") {
    event.preventDefault();
    highlightedIndex.value = Math.max(highlightedIndex.value - 1, 0);
    return;
  }

  if (event.key === "Enter") {
    event.preventDefault();
    const code = filteredCodes.value[highlightedIndex.value];
    if (highlightedIndex.value >= 0 && code !== undefined) {
      selectCode(code);
    }
  }
}
</script>

<template>
  <div>
    <label class="label" :for="id ?? 'stock-code'">
      {{ label ?? "Kode saham" }}
    </label>
    <div class="relative">
      <input
        :id="id ?? 'stock-code'"
        :value="model"
        type="text"
        maxlength="10"
        class="input uppercase"
        :placeholder="placeholder ?? 'Contoh: BBCA'"
        autocomplete="off"
        spellcheck="false"
        @input="onInput"
        @focus="onFocus"
        @blur="onBlur"
        @keydown="onKeydown"
      >
      <ul
        v-if="isOpen && filteredCodes.length > 0"
        class="absolute z-10 mt-1 max-h-48 w-full overflow-y-auto rounded-lg border border-gray-200 bg-white shadow-soft dark:border-gray-700 dark:bg-gray-900"
        role="listbox"
      >
        <li
          v-for="(code, index) in filteredCodes"
          :key="code"
          class="cursor-pointer px-3 py-1.5 text-sm text-gray-900 first:rounded-t-lg last:rounded-b-lg dark:text-gray-100"
          :class="{
            'bg-primary-50 text-primary-800 dark:bg-primary-950 dark:text-primary-200': index === highlightedIndex,
            'hover:bg-gray-100 dark:hover:bg-gray-800': index !== highlightedIndex,
          }"
          role="option"
          :aria-selected="index === highlightedIndex"
          @mousedown.prevent="selectCode(code)"
        >
          {{ code }}
        </li>
      </ul>
      <p
        v-if="isOpen && filteredCodes.length === 0 && stockCodes.length > 0"
        class="absolute z-10 mt-1 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-500 shadow-soft dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400"
      >
        Tidak ada kode yang cocok.
      </p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { normalizeStockCode } from "@/shared/utils/stock-code";

const model = defineModel<string>({ required: true });

defineProps<{
  id?: string;
  label?: string;
  placeholder?: string;
}>();

function onInput(event: Event): void {
  const target = event.target as HTMLInputElement;
  model.value = normalizeStockCode(target.value);
}
</script>

<template>
  <div>
    <label class="label" :for="id ?? 'stock-code'">
      {{ label ?? "Kode saham" }}
    </label>
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
    >
  </div>
</template>

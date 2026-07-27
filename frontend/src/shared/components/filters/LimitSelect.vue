<script setup lang="ts">
const model = defineModel<number>({ required: true });

withDefaults(
  defineProps<{
    id?: string;
    label?: string;
    options?: number[];
  }>(),
  {
    id: "limit-select",
    label: "Limit",
    options: () => [5, 10, 20, 50],
  },
);

function onChange(event: Event): void {
  const target = event.target as HTMLSelectElement;
  model.value = Number(target.value);
}
</script>

<template>
  <div>
    <label class="label" :for="id">{{ label }}</label>
    <select :id="id" class="input" :value="model" @change="onChange">
      <option v-for="option in options" :key="option" :value="option">
        {{ option }}
      </option>
    </select>
  </div>
</template>

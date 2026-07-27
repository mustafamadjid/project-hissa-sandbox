<script setup lang="ts">
const startDate = defineModel<string>("startDate", { required: true });
const endDate = defineModel<string>("endDate", { required: true });

defineProps<{
  idPrefix?: string;
}>();
</script>

<template>
  <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
    <div class="min-w-0 flex-1">
      <label class="label" :for="`${idPrefix ?? 'filter'}-start-date`">
        Tanggal mulai
      </label>
      <input
        :id="`${idPrefix ?? 'filter'}-start-date`"
        v-model="startDate"
        type="date"
        class="input"
      >
    </div>
    <div class="min-w-0 flex-1">
      <label class="label" :for="`${idPrefix ?? 'filter'}-end-date`">
        Tanggal akhir
      </label>
      <input
        :id="`${idPrefix ?? 'filter'}-end-date`"
        v-model="endDate"
        type="date"
        class="input"
      >
    </div>
  </div>
</template>

<script setup lang="ts">
defineEmits<{
  retry: [];
}>();
</script>

<template>
  <button type="button" class="btn btn-primary" @click="$emit('retry')">
    Coba lagi
  </button>
</template>

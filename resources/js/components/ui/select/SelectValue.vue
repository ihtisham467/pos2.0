<script setup lang="ts">
import { inject, computed } from 'vue';

interface SelectContext {
  open: Ref<boolean>;
  setOpen: (value: boolean) => void;
  value: Ref<string>;
  setValue: (value: string) => void;
}

const props = defineProps<{
  placeholder?: string;
}>();

const selectContext = inject<SelectContext>('select');
const displayValue = computed(() => {
  if (selectContext?.value.value) {
    return selectContext.value.value;
  }
  return props.placeholder || '';
});
</script>

<template>
  <span>{{ displayValue }}</span>
</template>

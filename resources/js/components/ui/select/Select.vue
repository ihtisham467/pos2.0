<script setup lang="ts">
import { provide, ref, type Ref } from 'vue';
import { cn } from '@/lib/utils';

interface SelectContext {
  open: Ref<boolean>;
  setOpen: (value: boolean) => void;
  value: Ref<string>;
  setValue: (value: string) => void;
}

const props = defineProps<{
  defaultValue?: string;
  class?: string;
}>();

const open = ref(false);
const value = ref(props.defaultValue || '');
const setOpen = (newOpen: boolean) => {
  open.value = newOpen;
};
const setValue = (newValue: string) => {
  value.value = newValue;
  open.value = false;
};

provide<SelectContext>('select', {
  open,
  setOpen,
  value,
  setValue,
});
</script>

<template>
  <div :class="cn('relative', props.class || '')">
    <slot />
  </div>
</template>

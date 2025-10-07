<script setup lang="ts">
import { inject, computed } from 'vue';
import { cn } from '@/lib/utils';

interface SelectContext {
  open: Ref<boolean>;
  setOpen: (value: boolean) => void;
  value: Ref<string>;
  setValue: (value: string) => void;
}

const props = defineProps<{
  value: string;
  class?: string;
}>();

const selectContext = inject<SelectContext>('select');
const isSelected = computed(() => selectContext?.value.value === props.value);

const handleClick = () => {
  selectContext?.setValue(props.value);
};
</script>

<template>
  <div
    :class="cn(
      'relative flex w-full cursor-pointer select-none items-center rounded-sm py-1.5 pl-8 pr-2 text-sm outline-none hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground',
      isSelected && 'bg-accent text-accent-foreground',
      props.class || ''
    )"
    @click="handleClick"
  >
    <span class="absolute left-2 flex h-3.5 w-3.5 items-center justify-center">
      <span
        v-if="isSelected"
        class="h-2 w-2 rounded-full bg-current"
      />
    </span>
    <slot />
  </div>
</template>

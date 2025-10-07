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
  class?: string;
}>();

const selectContext = inject<SelectContext>('select');
const isOpen = computed(() => selectContext?.open.value || false);
</script>

<template>
  <div
    v-if="isOpen"
    :class="cn(
      'absolute top-full z-50 mt-1 max-h-60 w-full overflow-auto rounded-md border bg-popover text-popover-foreground shadow-md',
      props.class || ''
    )"
  >
    <div class="p-1">
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import { inject, computed } from 'vue';
import { cn } from '@/lib/utils';
import { ChevronDown } from 'lucide-vue-next';

interface SelectContext {
  open: Ref<boolean>;
  setOpen: (value: boolean) => void;
  value: Ref<string>;
  setValue: (value: string) => void;
}

const props = defineProps<{
  class?: string;
  placeholder?: string;
}>();

const selectContext = inject<SelectContext>('select');
const isOpen = computed(() => selectContext?.open.value || false);

const handleClick = () => {
  selectContext?.setOpen(!isOpen.value);
};
</script>

<template>
  <button
    type="button"
    :class="cn(
      'flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
      props.class || ''
    )"
    @click="handleClick"
  >
    <span v-if="selectContext?.value.value" class="truncate">
      <slot :value="selectContext.value.value" />
    </span>
    <span v-else class="text-muted-foreground">
      {{ props.placeholder }}
    </span>
    <ChevronDown class="h-4 w-4 opacity-50" />
  </button>
</template>

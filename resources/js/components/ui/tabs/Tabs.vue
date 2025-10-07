<script setup lang="ts">
import { provide, ref, type Ref } from 'vue';
import { cn } from '@/lib/utils';

interface TabsContext {
  activeTab: Ref<string>;
  setActiveTab: (value: string) => void;
}

const props = defineProps<{
  defaultValue?: string;
  class?: string;
}>();

const activeTab = ref(props.defaultValue || '');
const setActiveTab = (value: string) => {
  activeTab.value = value;
};

provide<TabsContext>('tabs', {
  activeTab,
  setActiveTab,
});
</script>

<template>
  <div :class="cn('w-full', props.class || '')">
    <slot />
  </div>
</template>

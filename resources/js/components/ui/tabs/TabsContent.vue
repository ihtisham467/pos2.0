<script setup lang="ts">
import { inject, computed } from 'vue';
import { cn } from '@/lib/utils';

interface TabsContext {
  activeTab: Ref<string>;
  setActiveTab: (value: string) => void;
}

const props = defineProps<{
  value: string;
  class?: string;
}>();

const tabsContext = inject<TabsContext>('tabs');
const isActive = computed(() => tabsContext?.activeTab.value === props.value);
</script>

<template>
  <div
    v-if="isActive"
    :class="cn(
      'mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2',
      props.class || ''
    )"
  >
    <slot />
  </div>
</template>

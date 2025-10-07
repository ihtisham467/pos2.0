<script setup lang="ts">
import { cn } from '@/lib/utils';

interface Props {
  class?: string;
  rows?: number;
  placeholder?: string;
  name?: string;
  id?: string;
  required?: boolean;
  disabled?: boolean;
  readonly?: boolean;
  'default-value'?: string;
}

const props = withDefaults(defineProps<Props>(), {
  rows: 3,
});
</script>

<template>
  <textarea
    :id="props.id"
    :name="props.name"
    :rows="props.rows"
    :placeholder="props.placeholder"
    :required="props.required"
    :disabled="props.disabled"
    :readonly="props.readonly"
    :default-value="props['default-value']"
    :class="cn(
      'flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
      props.class || ''
    )"
  />
</template>

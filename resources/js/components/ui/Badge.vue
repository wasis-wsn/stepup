<template>
  <span
    :class="
      cn(
        'inline-flex items-center px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider select-none',
        variantClasses[variant],
        $attrs.class as string
      )
    "
  >
    <slot />
  </span>
</template>

<script setup lang="ts">
import { cn } from '@/lib/utils';

interface Props {
  variant?: 'default' | 'secondary' | 'outline' | 'dark';
}

withDefaults(defineProps<Props>(), {
  variant: 'default',
});

const variantClasses = {
  default: 'bg-[#F5F5F3] text-[#111111] border border-[#E7E7E5]',
  secondary: 'bg-white text-[#707070] border border-[#E7E7E5]',
  outline: 'bg-transparent text-[#111111] border border-[#111111]',
  dark: 'bg-[#111111] text-white border border-[#111111]',
};
</script>

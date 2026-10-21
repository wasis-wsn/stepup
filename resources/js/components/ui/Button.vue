<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="
      cn(
        'inline-flex items-center justify-center text-xs font-semibold uppercase tracking-wider transition-all duration-200 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-black disabled:pointer-events-none disabled:opacity-40 select-none cursor-pointer',
        variantClasses[variant],
        sizeClasses[size],
        $attrs.class as string
      )
    "
  >
    <svg
      v-if="loading"
      class="mr-2 h-3.5 w-3.5 animate-spin"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
    >
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    <slot />
  </button>
</template>

<script setup lang="ts">
import { cn } from '@/lib/utils';

interface Props {
  variant?: 'default' | 'outline' | 'secondary' | 'ghost' | 'destructive';
  size?: 'sm' | 'md' | 'lg' | 'icon';
  type?: 'button' | 'submit' | 'reset';
  disabled?: boolean;
  loading?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
  size: 'md',
  type: 'button',
  disabled: false,
  loading: false,
});

const variantClasses = {
  default: 'bg-[#111111] text-white hover:bg-neutral-800 active:bg-neutral-900 border border-[#111111]',
  outline: 'bg-transparent text-[#111111] border border-[#E7E7E5] hover:border-[#111111] hover:bg-[#F5F5F3] active:bg-[#ECECE9]',
  secondary: 'bg-[#F5F5F3] text-[#111111] border border-[#E7E7E5] hover:bg-[#EAEAE7]',
  ghost: 'bg-transparent text-[#111111] hover:bg-[#F5F5F3]',
  destructive: 'bg-red-600 text-white hover:bg-red-700 active:bg-red-800 border border-red-600',
};

const sizeClasses = {
  sm: 'h-8 px-3 text-[11px]',
  md: 'h-10 px-5 text-xs',
  lg: 'h-12 px-7 text-xs font-bold tracking-widest',
  icon: 'h-10 w-10 p-0',
};
</script>

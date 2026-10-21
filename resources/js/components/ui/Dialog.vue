<template>
  <teleport to="body">
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="open"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs overflow-y-auto"
        @click.self="$emit('update:open', false)"
        @keydown.esc="$emit('update:open', false)"
      >
        <div
          :class="
            cn(
              'relative w-full max-w-lg bg-white border border-[#E7E7E5] p-6 sm:p-8 shadow-2xl transition-all',
              $attrs.class as string
            )
          "
          role="dialog"
          aria-modal="true"
        >
          <button
            type="button"
            @click="$emit('update:open', false)"
            class="absolute top-5 right-5 text-[#707070] hover:text-[#111111] transition-colors p-1"
            aria-label="Close dialog"
          >
            ✕
          </button>
          <slot />
        </div>
      </div>
    </transition>
  </teleport>
</template>

<script setup lang="ts">
import { cn } from '@/lib/utils';

interface Props {
  open: boolean;
}

defineProps<Props>();

defineEmits<{
  (e: 'update:open', value: boolean): void;
}>();
</script>

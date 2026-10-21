<template>
  <div class="w-full space-y-1.5">
    <label v-if="label" class="block text-[11px] font-semibold uppercase tracking-wider text-[#707070]">
      {{ label }}
      <span v-if="required" class="text-black">*</span>
    </label>

    <div class="relative">
      <input
        :value="modelValue"
        @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
        :type="type"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :min="min"
        :max="max"
        :step="step"
        :class="
          cn(
            'flex h-10 w-full rounded-none border border-[#E7E7E5] bg-white px-3.5 py-2 text-xs text-[#111111] placeholder:text-[#999999] focus-visible:border-[#111111] focus-visible:outline-none transition-colors disabled:cursor-not-allowed disabled:bg-[#F5F5F3]',
            error && 'border-red-500 focus-visible:border-red-500',
            $attrs.class as string
          )
        "
      />
      <slot name="suffix" />
    </div>

    <p v-if="error" class="text-[11px] text-red-500 font-medium">
      {{ error }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { cn } from '@/lib/utils';

interface Props {
  modelValue?: string | number;
  label?: string;
  type?: string;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
  error?: string;
  min?: number | string;
  max?: number | string;
  step?: number | string;
}

withDefaults(defineProps<Props>(), {
  modelValue: '',
  label: '',
  type: 'text',
  placeholder: '',
  required: false,
  disabled: false,
  error: '',
});

defineEmits<{
  (e: 'update:modelValue', value: string): void;
}>();
</script>

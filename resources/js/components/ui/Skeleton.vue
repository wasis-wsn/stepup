<template>
  <div
    :class="
      cn(
        'animate-pulse bg-[#F0F0EE]',
        $attrs.class as string
      )
    "
  />
</template>

<script setup lang="ts">
import { cn } from '@/lib/utils';
</script>

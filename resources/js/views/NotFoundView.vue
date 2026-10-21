<template>
  <div class="min-h-[70vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8 text-center">
    <div class="max-w-md w-full space-y-6">
      <span class="text-[11px] font-bold uppercase tracking-widest text-[#707070]">
        404 Error
      </span>
      <h1 class="text-4xl sm:text-5xl font-black tracking-tight text-[#111111]">
        Page Not Found
      </h1>
      <p class="text-xs sm:text-sm text-[#707070] leading-relaxed">
        The page you requested could not be located. It may have moved or no longer exists.
      </p>
      <div class="pt-2">
        <router-link to="/">
          <Button size="md">
            Return to Home
          </Button>
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import Button from '@/components/ui/Button.vue';
</script>

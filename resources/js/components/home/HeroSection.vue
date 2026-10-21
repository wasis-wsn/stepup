<template>
  <section class="relative bg-white pt-6 pb-16 sm:py-20 lg:py-28 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
        <!-- Left Editorial Copy -->
        <div class="lg:col-span-6 space-y-6 sm:space-y-8 text-center lg:text-left z-10">
          <div class="inline-flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-black"></span>
            <span class="text-[11px] font-bold uppercase tracking-widest text-[#707070]">
              SS26 Footwear Collection
            </span>
          </div>

          <h1 class="text-4xl sm:text-6xl lg:text-7xl font-black tracking-tight text-[#111111] leading-[1.05]">
            Every step tells a story.
          </h1>

          <p class="text-[#707070] text-sm sm:text-base leading-relaxed max-w-lg mx-auto lg:mx-0">
            Engineered silhouettes built for all-day comfort, clean lines, and purposeful movement. Discover footwear designed to elevate every stride.
          </p>

          <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
            <router-link to="/products">
              <Button size="lg" class="w-full sm:w-auto">
                Explore Collection
              </Button>
            </router-link>

            <a
              href="#featured"
              class="text-xs font-semibold uppercase tracking-wider text-[#111111] hover:text-[#707070] transition-colors py-3 px-4 flex items-center gap-1.5"
            >
              <span>Discover our picks</span>
              <span>↓</span>
            </a>
          </div>

          <!-- Quick Metadata stats -->
          <div class="pt-8 border-t border-[#E7E7E5] grid grid-cols-3 gap-6 max-w-md mx-auto lg:mx-0 text-left">
            <div>
              <p class="text-lg sm:text-xl font-bold text-[#111111]">100%</p>
              <p class="text-[10px] uppercase font-semibold text-[#707070] tracking-wider">Ergonomic Fit</p>
            </div>
            <div>
              <p class="text-lg sm:text-xl font-bold text-[#111111]">3</p>
              <p class="text-[10px] uppercase font-semibold text-[#707070] tracking-wider">Curated Series</p>
            </div>
            <div>
              <p class="text-lg sm:text-xl font-bold text-[#111111]">Zero</p>
              <p class="text-[10px] uppercase font-semibold text-[#707070] tracking-wider">Compromise</p>
            </div>
          </div>
        </div>

        <!-- Right Large Visual Composition -->
        <div class="lg:col-span-6 relative flex items-center justify-center">
          <!-- Geometric background shape -->
          <div class="w-full aspect-4/3 max-w-lg sm:max-w-xl bg-[#F5F5F3] relative overflow-hidden flex items-center justify-center p-8 sm:p-12">
            <!-- Subtle background circle -->
            <div class="absolute w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-white/60 blur-2xl"></div>

            <img
              src="/storage/products/aerorun-pro.png"
              alt="STEPUP AeroRun Pro Minimalist Sneaker"
              class="relative z-10 w-full h-auto max-h-80 sm:max-h-96 object-contain drop-shadow-2xl transform -rotate-12 hover:rotate-0 transition-transform duration-700 ease-out"
              @error="handleImgFallback"
            />

            <!-- Corner caption tag -->
            <div class="absolute bottom-4 left-4 z-20 bg-white/90 backdrop-blur-xs px-3 py-1.5 border border-[#E7E7E5] text-[10px] uppercase font-bold tracking-wider text-[#111111]">
              AeroRun Pro — Lightweight Tempo
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import Button from '@/components/ui/Button.vue';

function handleImgFallback(e: Event) {
  const target = e.target as HTMLImageElement;
  target.src = '/storage/products/urban-classic.png';
}
</script>

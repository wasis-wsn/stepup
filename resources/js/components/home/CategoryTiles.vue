<template>
  <section class="py-16 sm:py-24 border-t border-[#E7E7E5] bg-[#F5F5F3]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-10 sm:mb-14">
        <span class="text-[11px] font-bold uppercase tracking-widest text-[#707070]">
          Series & Categories
        </span>
        <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-[#111111] mt-1">
          Shop by Category
        </h2>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
        <!-- Running Shoes -->
        <router-link
          to="/products?category=running-shoes"
          class="group bg-white border border-[#E7E7E5] hover:border-[#111111] transition-all duration-300 p-8 flex flex-col justify-between aspect-4/5 relative overflow-hidden"
        >
          <div class="z-10">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#707070]">
              Performance
            </span>
            <h3 class="text-xl sm:text-2xl font-black text-[#111111] mt-1 tracking-tight">
              Running Shoes
            </h3>
            <p class="text-xs text-[#707070] mt-2 max-w-xs">
              Ultra-lightweight foam cushioning engineered for tempo, cadence, and mileage.
            </p>
          </div>

          <div class="relative w-full h-48 flex items-center justify-center my-4">
            <img
              src="/storage/products/aerorun-white.jpg"
              alt="Running Shoes"
              class="max-h-full object-contain img-hover-scale drop-shadow-md"
            />
          </div>

          <div class="z-10 flex items-center justify-between text-xs font-bold uppercase tracking-wider text-[#111111] border-t border-[#F5F5F3] pt-4">
            <span>Explore Series</span>
            <span class="group-hover:translate-x-1 transition-transform">→</span>
          </div>
        </router-link>

        <!-- Casual Sneakers -->
        <router-link
          to="/products?category=casual-sneakers"
          class="group bg-white border border-[#E7E7E5] hover:border-[#111111] transition-all duration-300 p-8 flex flex-col justify-between aspect-4/5 relative overflow-hidden"
        >
          <div class="z-10">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#707070]">
              Everyday Wear
            </span>
            <h3 class="text-xl sm:text-2xl font-black text-[#111111] mt-1 tracking-tight">
              Casual Sneakers
            </h3>
            <p class="text-xs text-[#707070] mt-2 max-w-xs">
              Minimalist silhouettes, durable canvas, and versatile tones for everyday motion.
            </p>
          </div>

          <div class="relative w-full h-48 flex items-center justify-center my-4">
            <img
              src="/storage/products/urban-black-gum.jpg"
              alt="Casual Sneakers"
              class="max-h-full object-contain img-hover-scale drop-shadow-md"
            />
          </div>

          <div class="z-10 flex items-center justify-between text-xs font-bold uppercase tracking-wider text-[#111111] border-t border-[#F5F5F3] pt-4">
            <span>Explore Series</span>
            <span class="group-hover:translate-x-1 transition-transform">→</span>
          </div>
        </router-link>

        <!-- Formal Shoes -->
        <router-link
          to="/products?category=formal-shoes"
          class="group bg-white border border-[#E7E7E5] hover:border-[#111111] transition-all duration-300 p-8 flex flex-col justify-between aspect-4/5 relative overflow-hidden"
        >
          <div class="z-10">
            <span class="text-[10px] font-bold uppercase tracking-widest text-[#707070]">
              Artisanal Craft
            </span>
            <h3 class="text-xl sm:text-2xl font-black text-[#111111] mt-1 tracking-tight">
              Formal Shoes
            </h3>
            <p class="text-xs text-[#707070] mt-2 max-w-xs">
              Handcrafted full-grain leather derbys and oxfords for refined occasions.
            </p>
          </div>

          <div class="relative w-full h-48 flex items-center justify-center my-4">
            <img
              src="/storage/products/oxford-monarch-cognac.jpg"
              alt="Formal Shoes"
              class="max-h-full object-contain img-hover-scale drop-shadow-md filter contrast-125"
            />
          </div>

          <div class="z-10 flex items-center justify-between text-xs font-bold uppercase tracking-wider text-[#111111] border-t border-[#F5F5F3] pt-4">
            <span>Explore Series</span>
            <span class="group-hover:translate-x-1 transition-transform">→</span>
          </div>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
// Stateless category tiles linking to query-filtered catalog
</script>

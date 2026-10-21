<template>
  <section id="featured" class="py-16 sm:py-24 border-t border-[#E7E7E5] bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <!-- Section Header -->
      <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 sm:mb-14 gap-4">
        <div class="space-y-2">
          <span class="text-[11px] font-bold uppercase tracking-widest text-[#707070]">
            Curated Showcase
          </span>
          <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-[#111111]">
            Selected for You
          </h2>
          <p class="text-[#707070] text-xs sm:text-sm max-w-lg">
            Essential footwear handpicked for versatility, comfort, and timeless contemporary aesthetics.
          </p>
        </div>

        <router-link
          to="/products"
          class="text-xs font-bold uppercase tracking-wider text-[#111111] hover:text-[#707070] transition-colors flex items-center gap-1.5 self-start sm:self-auto"
        >
          <span>View all products</span>
          <span>→</span>
        </router-link>
      </div>

      <!-- Loading Skeletons -->
      <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="n in 4" :key="n" class="border border-[#E7E7E5] p-4 space-y-4">
          <Skeleton class="w-full aspect-square" />
          <Skeleton class="h-4 w-3/4" />
          <Skeleton class="h-3 w-1/2" />
        </div>
      </div>

      <!-- Products Grid -->
      <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <ProductCard
          v-for="product in featuredProducts"
          :key="product.id"
          :product="product"
        />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import type { Product } from '@/types';
import productService from '@/services/productService';
import ProductCard from '@/components/products/ProductCard.vue';
import Skeleton from '@/components/ui/Skeleton.vue';

const featuredProducts = ref<Product[]>([]);
const loading = ref(true);

onMounted(async () => {
  try {
    const res = await productService.getProducts({ per_page: 4 });
    featuredProducts.value = res.data.slice(0, 4);
  } catch (err) {
    console.error('Failed to load featured products', err);
  } finally {
    loading.value = false;
  }
});
</script>

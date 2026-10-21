<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-widest text-[#707070] mb-8">
      <router-link to="/" class="hover:text-[#111111] transition-colors">Home</router-link>
      <span>/</span>
      <router-link to="/products" class="hover:text-[#111111] transition-colors">The Collection</router-link>
      <span v-if="product">/</span>
      <span v-if="product" class="text-[#111111] truncate max-w-xs">{{ product.name }}</span>
    </nav>

    <!-- Loading State -->
    <div v-if="loading" class="grid grid-cols-1 lg:grid-cols-12 gap-12">
      <div class="lg:col-span-7">
        <Skeleton class="w-full aspect-square" />
      </div>
      <div class="lg:col-span-5 space-y-6">
        <Skeleton class="h-6 w-1/4" />
        <Skeleton class="h-10 w-3/4" />
        <Skeleton class="h-8 w-1/3" />
        <Skeleton class="h-32 w-full" />
      </div>
    </div>

    <!-- Product Not Found State -->
    <div
      v-else-if="!product"
      class="border border-[#E7E7E5] p-16 text-center my-12 max-w-md mx-auto space-y-4"
    >
      <div class="text-3xl">🔍</div>
      <h2 class="text-xl font-bold uppercase tracking-wider text-[#111111]">Style Not Found</h2>
      <p class="text-xs text-[#707070]">
        The footwear style you are looking for does not exist or has been archived.
      </p>
      <router-link to="/products">
        <Button variant="default" size="sm" class="mt-2">
          Back to Collection
        </Button>
      </router-link>
    </div>

    <!-- Main Detail Presentation -->
    <div v-else class="space-y-20">
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">
        <!-- Left: Large Footwear Showcase -->
        <div class="lg:col-span-7 bg-[#F5F5F3] border border-[#E7E7E5] p-8 sm:p-14 flex items-center justify-center relative overflow-hidden aspect-square">
          <img
            v-if="product.image"
            :src="resolveImageUrl(product.image)"
            :alt="product.name"
            class="w-full h-full object-contain drop-shadow-xl img-hover-scale"
            @error="handleImgFallback"
          />
          <div v-else class="text-[#707070] text-xs font-semibold uppercase tracking-wider">
            Footwear Photography
          </div>

          <div class="absolute top-4 left-4">
            <Badge variant="dark" class="text-[9px]">
              {{ product.category?.name || 'Footwear' }}
            </Badge>
          </div>
        </div>

        <!-- Right: Editorial Specifications & Story -->
        <div class="lg:col-span-5 space-y-8">
          <div>
            <div class="flex items-center gap-3 mb-2">
              <span class="text-[11px] font-bold uppercase tracking-widest text-[#707070]">
                Style Code: {{ product.slug }}
              </span>
            </div>

            <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-[#111111]">
              {{ product.name }}
            </h1>

            <div class="mt-4 text-2xl font-extrabold text-[#111111]">
              {{ formatRupiah(product.price) }}
            </div>
          </div>

          <!-- Description -->
          <div class="border-t border-b border-[#E7E7E5] py-6 space-y-4">
            <h3 class="text-[11px] font-bold uppercase tracking-widest text-[#111111]">
              Design & Construction
            </h3>
            <p class="text-xs sm:text-sm text-[#707070] leading-relaxed">
              {{ product.description || 'Engineered with premium materials for maximum comfort and durability during everyday movement.' }}
            </p>
          </div>

          <!-- Editorial Highlights List -->
          <div class="space-y-3">
            <h4 class="text-[11px] font-bold uppercase tracking-widest text-[#111111]">
              Highlights
            </h4>
            <ul class="text-xs text-[#707070] space-y-2">
              <li class="flex items-start gap-2">
                <span class="font-bold text-black">&bull;</span>
                <span>Anatomically shaped footbed with arch support</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="font-bold text-black">&bull;</span>
                <span>Breathable, lightweight exterior construction</span>
              </li>
              <li class="flex items-start gap-2">
                <span class="font-bold text-black">&bull;</span>
                <span>Durable rubber compound traction outsole</span>
              </li>
            </ul>
          </div>

          <!-- Size Selector -->
          <div class="pt-2 space-y-3">
            <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-widest text-[#111111]">
              <span>Select Size (EU)</span>
              <span class="text-[#707070] font-normal normal-case text-xs">Standard Fit</span>
            </div>
            <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
              <button
                v-for="s in sizes"
                :key="s"
                type="button"
                @click="selectedSize = s"
                class="h-10 border text-xs font-bold transition-all flex items-center justify-center cursor-pointer"
                :class="selectedSize === s ? 'border-black bg-black text-white shadow-xs' : 'border-[#E7E7E5] bg-white text-[#111111] hover:border-black'"
              >
                {{ s }}
              </button>
            </div>
          </div>

          <!-- Actions -->
          <div class="pt-4 flex flex-col sm:flex-row gap-3">
            <button
              type="button"
              @click="handleAddToBag"
              class="flex-1 py-4 bg-[#111111] hover:bg-black text-white text-xs font-bold uppercase tracking-widest transition-all flex items-center justify-center gap-2 cursor-pointer shadow-xs hover:shadow"
            >
              <ShoppingBag class="w-4 h-4" />
              <span>Add to Bag &bull; {{ formatRupiah(product.price) }}</span>
            </button>
            <router-link to="/products" class="sm:w-auto">
              <Button variant="outline" size="lg" class="w-full sm:w-auto py-4">
                ← Catalog
              </Button>
            </router-link>
          </div>
        </div>
      </div>

      <!-- Related Footwear Suggestions -->
      <div v-if="relatedProducts.length > 0" class="pt-16 border-t border-[#E7E7E5]">
        <div class="flex items-center justify-between mb-8">
          <div>
            <span class="text-[11px] font-bold uppercase tracking-widest text-[#707070]">You might also appreciate</span>
            <h2 class="text-2xl font-black text-[#111111]">Related Footwear</h2>
          </div>
          <router-link to="/products" class="text-xs font-bold uppercase tracking-wider text-[#111111] hover:text-[#707070]">
            Explore All →
          </router-link>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <ProductCard
            v-for="item in relatedProducts"
            :key="item.id"
            :product="item"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { ShoppingBag } from 'lucide-vue-next';
import type { Product } from '@/types';
import productService from '@/services/productService';
import { formatRupiah, resolveImageUrl } from '@/lib/utils';
import { useCartStore } from '@/stores/cart';
import ProductCard from '@/components/products/ProductCard.vue';
import Skeleton from '@/components/ui/Skeleton.vue';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';

const route = useRoute();
const cart = useCartStore();

const product = ref<Product | null>(null);
const relatedProducts = ref<Product[]>([]);
const loading = ref(true);

const sizes = ['39', '40', '41', '42', '43', '44', '45'];
const selectedSize = ref('42');

function handleAddToBag() {
  if (product.value) {
    cart.addItem(product.value, selectedSize.value);
  }
}

async function loadProductData(id: string | number) {
  loading.value = true;
  product.value = null;
  relatedProducts.value = [];

  try {
    const fetched = await productService.getProductById(id);
    product.value = fetched;

    // Load related items in same category
    if (fetched.category?.slug) {
      const relatedRes = await productService.getProducts({
        category: fetched.category.slug,
        per_page: 4,
      });
      relatedProducts.value = relatedRes.data.filter(p => p.id !== fetched.id).slice(0, 3);
    }
  } catch (err) {
    console.error('Failed to load product detail', err);
  } finally {
    loading.value = false;
  }
}

function handleImgFallback(e: Event) {
  const target = e.target as HTMLImageElement;
  target.src = '/storage/products/urban-classic.png';
}

watch(() => route.params.id, (newId) => {
  if (newId) {
    loadProductData(newId as string);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});

onMounted(() => {
  if (route.params.id) {
    loadProductData(route.params.id as string);
  }
});
</script>

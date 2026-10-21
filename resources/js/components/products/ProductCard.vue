<template>
  <router-link
    :to="`/products/${product.id}`"
    class="group block bg-white border border-[#E7E7E5] hover:border-[#111111] transition-colors duration-300 relative flex flex-col justify-between"
  >
    <!-- Product Image Frame -->
    <div class="relative w-full aspect-square bg-[#F5F5F3] overflow-hidden flex items-center justify-center p-6">
      <img
        v-if="product.image"
        :src="resolveImageUrl(product.image)"
        :alt="product.name"
        loading="lazy"
        class="w-full h-full object-contain img-hover-scale drop-shadow-xs"
        @error="handleImgError"
      />
      <div v-else class="text-[#999999] text-xs font-semibold uppercase tracking-wider">
        No Image
      </div>

      <!-- Category Tag (top left) -->
      <div class="absolute top-3 left-3 z-10">
        <Badge variant="secondary" class="bg-white/90 backdrop-blur-xs text-[9px] font-bold">
          {{ product.category?.name || 'Footwear' }}
        </Badge>
      </div>

      <!-- Quick Add to Bag Button (bottom right on hover) -->
      <button
        type="button"
        @click.prevent.stop="onQuickAdd"
        class="absolute bottom-3 right-3 z-10 w-9 h-9 rounded-full bg-white/95 backdrop-blur-xs text-[#111111] hover:bg-black hover:text-white border border-[#E7E7E5] hover:border-black flex items-center justify-center transition-all opacity-90 sm:opacity-0 sm:group-hover:opacity-100 shadow-xs cursor-pointer"
        title="Add to Bag"
        aria-label="Add to Bag"
      >
        <ShoppingBag class="w-4 h-4" />
      </button>
    </div>

    <!-- Info Section -->
    <div class="p-4 sm:p-5 flex flex-col justify-between flex-1 border-t border-[#E7E7E5]">
      <div>
        <h3 class="text-xs sm:text-sm font-bold text-[#111111] tracking-tight group-hover:text-black transition-colors line-clamp-1">
          {{ product.name }}
        </h3>
        <p class="text-[11px] text-[#707070] line-clamp-2 mt-1 leading-relaxed">
          {{ product.description || 'Premium everyday footwear engineered for daily movement.' }}
        </p>
      </div>

      <div class="mt-4 pt-3 border-t border-[#F5F5F3] flex items-center justify-between">
        <span class="text-xs sm:text-sm font-black text-[#111111]">
          {{ formatRupiah(product.price) }}
        </span>
        <span class="text-[10px] font-bold uppercase tracking-wider text-[#707070] group-hover:text-black transition-colors flex items-center gap-1">
          View Detail →
        </span>
      </div>
    </div>
  </router-link>
</template>

<script setup lang="ts">
import { ShoppingBag } from 'lucide-vue-next';
import type { Product } from '@/types';
import { formatRupiah, resolveImageUrl } from '@/lib/utils';
import { useCartStore } from '@/stores/cart';
import Badge from '@/components/ui/Badge.vue';

const props = defineProps<{
  product: Product;
}>();

const cart = useCartStore();

function onQuickAdd() {
  cart.addItem(props.product, '42');
}

function handleImgError(e: Event) {
  const target = e.target as HTMLImageElement;
  target.style.display = 'none';
}
</script>

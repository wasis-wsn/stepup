<template>
  <div v-if="product" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in">
    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100 animate-scale-up">
      <!-- Close button -->
      <button
        @click="$emit('close')"
        class="absolute top-4 right-4 z-10 w-9 h-9 flex items-center justify-center rounded-full bg-white/80 hover:bg-white text-gray-700 hover:text-black border border-gray-200 transition-all shadow-sm"
      >
        ✕
      </button>

      <div class="grid grid-cols-1 md:grid-cols-2">
        <!-- Shoe Image Container -->
        <div class="bg-gradient-to-b from-gray-50 to-gray-100 flex items-center justify-center p-8 relative">
          <img
            v-if="product.image"
            :src="product.image"
            :alt="product.name"
            class="w-full h-64 object-contain transition-transform duration-500 hover:scale-105 drop-shadow-md"
            @error="handleImageError"
          />
          <div v-else class="w-full h-64 flex flex-col items-center justify-center text-gray-400">
            <svg class="w-16 h-16 stroke-1 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="text-xs uppercase tracking-wider font-semibold">No Image Available</span>
          </div>
        </div>

        <!-- Shoe Info -->
        <div class="p-8 flex flex-col justify-between">
          <div>
            <div class="flex items-center gap-2 mb-3">
              <span class="badge-tag bg-gray-100 text-gray-800 border border-gray-200">
                {{ product.category?.name || 'Footwear' }}
              </span>
              <span class="text-xs text-gray-400">ID #{{ product.id }}</span>
            </div>

            <h2 class="text-2xl font-bold text-gray-900 tracking-tight mb-2">
              {{ product.name }}
            </h2>

            <div class="flex items-baseline gap-2 mb-2">
              <span class="text-2xl font-extrabold text-black">
                {{ formatRupiah(product.discount && product.discount > 0 ? (product.discounted_price || (Number(product.price) * (100 - product.discount) / 100)) : product.price) }}
              </span>
              <span v-if="product.discount && product.discount > 0" class="text-sm text-gray-400 line-through font-semibold">
                {{ formatRupiah(product.price) }}
              </span>
              <span v-if="product.discount && product.discount > 0" class="text-xs bg-black text-white px-2 py-0.5 rounded font-bold">
                {{ product.discount }}% OFF
              </span>
            </div>

            <div v-if="product.color" class="text-xs text-gray-500 mb-4 flex items-center gap-1.5">
              <span class="font-bold text-black uppercase tracking-wider text-[10px]">Color:</span>
              <span>{{ product.color }}</span>
            </div>

            <!-- Description (Design & Construction) -->
            <div class="text-sm text-gray-600 leading-relaxed mb-4">
              <p class="font-bold text-[11px] uppercase tracking-wider text-black mb-1">Design & Construction</p>
              <p>{{ product.description || 'No description available for this shoe model.' }}</p>
            </div>

            <!-- Features Highlights -->
            <div class="border-t border-gray-100 pt-4 space-y-2">
              <p class="font-bold text-[11px] uppercase tracking-wider text-black mb-1">Highlights</p>
              <div
                v-for="(h, idx) in (product.detail ? product.detail.split('\n').map(l => l.replace(/^[•\-\*]\s*/, '').trim()).filter(Boolean) : ['Breathable engineered mesh upper', 'Ultra-responsive foam cushioning', 'Reinforced durable traction sole'])"
                :key="idx"
                class="flex items-center gap-2.5 text-xs text-gray-600"
              >
                <span class="w-1.5 h-1.5 rounded-full bg-black shrink-0"></span>
                <span>{{ h }}</span>
              </div>
            </div>

            <!-- Sizes Available -->
            <div class="border-t border-gray-100 pt-4 mt-4">
              <p class="font-bold text-[11px] uppercase tracking-wider text-black mb-2">Available Sizes</p>
              <div class="flex flex-wrap gap-1.5">
                <span
                  v-for="s in (product.sizes && Array.isArray(product.sizes) ? product.sizes : ['39', '40', '41', '42', '43', '44', '45'])"
                  :key="s"
                  class="px-2.5 py-1 text-xs font-semibold bg-gray-50 border border-gray-200 rounded text-gray-800"
                >
                  {{ s }}
                </span>
              </div>
            </div>
          </div>

          <div class="pt-6 border-t border-gray-100 mt-6 flex gap-3">
            <button
              @click="$emit('close')"
              class="w-full py-3 bg-black hover:bg-neutral-800 text-white font-semibold text-sm rounded-xl transition-all shadow-sm text-center"
            >
              Close Preview
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  product: {
    type: Object,
    default: null
  }
});

defineEmits(['close']);

function formatRupiah(val) {
  if (!val) return 'Rp 0';
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val);
}

function handleImageError(e) {
  e.target.style.display = 'none';
}
</script>

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

            <p class="text-2xl font-extrabold text-black mb-4">
              {{ formatRupiah(product.price) }}
            </p>

            <div class="text-sm text-gray-600 leading-relaxed mb-6">
              <p>{{ product.description || 'No description available for this shoe model.' }}</p>
            </div>

            <!-- Features Highlights -->
            <div class="border-t border-gray-100 pt-4 space-y-2.5">
              <div class="flex items-center gap-2.5 text-xs text-gray-600">
                <span class="w-1.5 h-1.5 rounded-full bg-black"></span>
                <span>Breathable engineered mesh upper</span>
              </div>
              <div class="flex items-center gap-2.5 text-xs text-gray-600">
                <span class="w-1.5 h-1.5 rounded-full bg-black"></span>
                <span>Ultra-responsive foam cushioning</span>
              </div>
              <div class="flex items-center gap-2.5 text-xs text-gray-600">
                <span class="w-1.5 h-1.5 rounded-full bg-black"></span>
                <span>Reinforced durable traction sole</span>
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

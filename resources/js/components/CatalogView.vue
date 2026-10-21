<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero / Visual Direction Banner -->
    <div class="relative bg-white rounded-3xl p-8 sm:p-12 mb-10 border border-gray-100 shadow-sm overflow-hidden flex flex-col md:flex-row items-center justify-between">
      <div class="max-w-xl z-10 text-center md:text-left mb-6 md:mb-0">
        <span class="inline-block px-3 py-1 bg-black text-white text-[11px] font-bold tracking-widest uppercase rounded-full mb-3">
          STEPUP Shoes Catalog
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 mb-3 leading-tight">
          Find your next step.
        </h1>
        <p class="text-gray-500 text-sm sm:text-base leading-relaxed mb-6">
          Discover shoes for every day, every move. High-performance runners, casual everyday sneakers, and handcrafted formal footwear.
        </p>
        <div class="flex flex-wrap gap-2 justify-center md:justify-start">
          <button
            v-for="cat in categories"
            :key="cat.id"
            @click="selectCategory(cat.slug)"
            class="px-4 py-2 text-xs font-semibold rounded-full border transition-all"
            :class="selectedCategory === cat.slug ? 'bg-black text-white border-black shadow-sm' : 'bg-gray-50 text-gray-700 hover:bg-gray-100 border-gray-200'"
          >
            {{ cat.name }}
          </button>
        </div>
      </div>
      <div class="relative w-64 sm:w-80 h-48 sm:h-56 flex items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-tr from-gray-100 to-transparent rounded-full filter blur-xl opacity-70"></div>
        <img
          src="/storage/products/aerorun-pro.png"
          alt="Featured Shoe"
          class="relative object-contain max-h-full drop-shadow-2xl transform -rotate-12 hover:rotate-0 transition-transform duration-500"
          @error="handleHeroImgError"
        />
      </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white rounded-2xl p-5 mb-8 border border-gray-100 shadow-sm space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
        <!-- Search Input -->
        <div class="md:col-span-6 relative">
          <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <input
            v-model="searchQuery"
            @input="debounceFetch"
            type="text"
            placeholder="Search by shoe name (e.g. AeroRun, Classic, Derby)..."
            class="w-full pl-10 pr-9 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm bg-gray-50/50 focus:bg-white transition"
          />
          <button
            v-if="searchQuery"
            @click="searchQuery = ''; fetchProducts()"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 hover:text-black"
          >
            ✕
          </button>
        </div>

        <!-- Category Dropdown / Select -->
        <div class="md:col-span-3">
          <select
            v-model="selectedCategory"
            @change="fetchProducts(1)"
            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm bg-gray-50/50 focus:bg-white transition text-gray-700"
          >
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
              {{ cat.name }}
            </option>
          </select>
        </div>

        <!-- Filter toggle or clear button -->
        <div class="md:col-span-3 flex items-center gap-2">
          <button
            @click="showPriceFilter = !showPriceFilter"
            class="flex-1 py-2.5 px-3.5 rounded-xl border text-sm font-semibold transition flex items-center justify-center gap-2"
            :class="hasActivePriceFilter ? 'bg-black text-white border-black' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100'"
          >
            <span>💰</span>
            <span>Price Range</span>
            <span v-if="hasActivePriceFilter" class="w-2 h-2 rounded-full bg-emerald-400"></span>
          </button>

          <button
            v-if="hasActiveFilters"
            @click="resetAllFilters"
            class="py-2.5 px-3 rounded-xl border border-gray-200 hover:bg-red-50 hover:border-red-200 hover:text-red-600 text-gray-500 text-xs font-semibold transition"
            title="Reset Filters"
          >
            Reset
          </button>
        </div>
      </div>

      <!-- Expandable Price Filter Panel -->
      <transition name="slide-fade">
        <div v-if="showPriceFilter" class="pt-4 border-t border-gray-100 flex flex-col md:flex-row items-center justify-between gap-4">
          <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            <div class="flex items-center gap-2">
              <span class="text-xs font-semibold text-gray-500 uppercase">Min Rp</span>
              <input
                v-model="minPrice"
                type="number"
                placeholder="0"
                min="0"
                step="50000"
                class="w-28 px-3 py-1.5 rounded-lg border border-gray-200 focus:outline-none focus:border-black text-xs"
              />
            </div>
            <span class="text-gray-400">—</span>
            <div class="flex items-center gap-2">
              <span class="text-xs font-semibold text-gray-500 uppercase">Max Rp</span>
              <input
                v-model="maxPrice"
                type="number"
                placeholder="1000000"
                min="0"
                step="50000"
                class="w-28 px-3 py-1.5 rounded-lg border border-gray-200 focus:outline-none focus:border-black text-xs"
              />
            </div>
            <button
              @click="applyPriceFilter"
              class="px-4 py-1.5 bg-black hover:bg-neutral-800 text-white rounded-lg text-xs font-semibold transition shadow-sm"
            >
              Apply
            </button>
          </div>

          <!-- Price Presets -->
          <div class="flex flex-wrap gap-1.5 text-xs">
            <button
              @click="setPricePreset(0, 400000)"
              class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-700 transition"
            >
              &lt; Rp 400k
            </button>
            <button
              @click="setPricePreset(400000, 700000)"
              class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-700 transition"
            >
              Rp 400k - 700k
            </button>
            <button
              @click="setPricePreset(700000, null)"
              class="px-2.5 py-1 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-700 transition"
            >
              &gt; Rp 700k
            </button>
          </div>
        </div>
      </transition>

      <!-- Price error warning -->
      <div v-if="priceError" class="p-2.5 bg-red-50 border border-red-200 rounded-lg text-xs text-red-600 font-medium">
        {{ priceError }}
      </div>
    </div>

    <!-- Active Filters Summary & Count -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-2">
        <h2 class="text-lg font-bold text-gray-900 tracking-tight">Catalog Products</h2>
        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">
          {{ meta.total || 0 }} shoes
        </span>
      </div>
      <div class="text-xs text-gray-400">
        Showing page {{ meta.current_page || 1 }} of {{ meta.last_page || 1 }}
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div v-for="n in 8" :key="n" class="bg-white rounded-2xl p-4 border border-gray-100 animate-pulse">
        <div class="w-full h-52 bg-gray-100 rounded-xl mb-4"></div>
        <div class="h-4 bg-gray-100 rounded w-1/3 mb-2"></div>
        <div class="h-5 bg-gray-100 rounded w-3/4 mb-2"></div>
        <div class="h-4 bg-gray-100 rounded w-1/2"></div>
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="products.length === 0"
      class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm max-w-lg mx-auto my-8"
    >
      <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
        👟
      </div>
      <h3 class="text-lg font-bold text-gray-900 mb-1">No shoes found</h3>
      <p class="text-sm text-gray-500 mb-6">
        No footwear matched your search and filter criteria. Try adjusting or clearing your filters.
      </p>
      <button
        @click="resetAllFilters"
        class="px-5 py-2.5 bg-black hover:bg-neutral-800 text-white rounded-xl text-xs font-semibold transition"
      >
        Clear All Filters
      </button>
    </div>

    <!-- Products Grid -->
    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div
        v-for="product in products"
        :key="product.id"
        @click="$emit('select-product', product)"
        class="transition-card bg-white rounded-2xl p-4 border border-gray-100 cursor-pointer flex flex-col justify-between group"
      >
        <div>
          <!-- Shoe Image Area -->
          <div class="w-full h-56 bg-[#f7f7f8] rounded-xl flex items-center justify-center p-6 relative overflow-hidden group-hover:bg-[#f3f4f6] transition-colors">
            <img
              v-if="product.image"
              :src="product.image"
              :alt="product.name"
              class="w-full h-full object-contain drop-shadow-sm transition-transform duration-300 group-hover:scale-105"
              loading="lazy"
              @error="handleCardImgError"
            />
            <div v-else class="flex flex-col items-center justify-center text-gray-300">
              <span class="text-3xl mb-1">👟</span>
              <span class="text-[10px] uppercase font-bold text-gray-400">STEPUP</span>
            </div>
            <!-- Quick View overlay badge on hover -->
            <span class="absolute bottom-3 opacity-0 group-hover:opacity-100 transition-opacity bg-black/85 text-white text-[11px] font-semibold px-3 py-1 rounded-full shadow-md backdrop-blur-sm">
              Quick View
            </span>
          </div>

          <!-- Product Details -->
          <div class="mt-4">
            <div class="flex items-center justify-between mb-1">
              <span class="badge-tag bg-gray-100 text-gray-600">
                {{ product.category?.name || 'Shoes' }}
              </span>
              <span class="text-[11px] text-gray-400">#{{ product.id }}</span>
            </div>
            <h3 class="font-bold text-gray-900 text-base group-hover:text-black line-clamp-1">
              {{ product.name }}
            </h3>
            <p class="text-xs text-gray-500 line-clamp-2 mt-1 leading-relaxed">
              {{ product.description || 'Lightweight construction engineered for maximum comfort.' }}
            </p>
          </div>
        </div>

        <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between">
          <span class="text-base font-extrabold text-black">
            {{ formatRupiah(product.price) }}
          </span>
          <span class="text-xs font-semibold text-gray-400 group-hover:text-black transition-colors flex items-center gap-1">
            Details →
          </span>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="meta.last_page > 1" class="mt-10 flex items-center justify-center gap-2">
      <button
        @click="fetchProducts(meta.current_page - 1)"
        :disabled="meta.current_page <= 1"
        class="px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
      >
        ← Previous
      </button>

      <div class="flex items-center gap-1">
        <button
          v-for="page in paginationPages"
          :key="page"
          @click="fetchProducts(page)"
          class="w-9 h-9 rounded-xl text-xs font-semibold transition"
          :class="page === meta.current_page ? 'bg-black text-white' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50'"
        >
          {{ page }}
        </button>
      </div>

      <button
        @click="fetchProducts(meta.current_page + 1)"
        :disabled="meta.current_page >= meta.last_page"
        class="px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition"
      >
        Next →
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

defineEmits(['select-product']);

const products = ref([]);
const categories = ref([]);
const meta = ref({});
const loading = ref(false);

const searchQuery = ref('');
const selectedCategory = ref('');
const minPrice = ref('');
const maxPrice = ref('');
const showPriceFilter = ref(false);
const priceError = ref('');

let debounceTimer = null;

const hasActivePriceFilter = computed(() => {
  return minPrice.value !== '' || maxPrice.value !== '';
});

const hasActiveFilters = computed(() => {
  return searchQuery.value !== '' || selectedCategory.value !== '' || hasActivePriceFilter.value;
});

const paginationPages = computed(() => {
  if (!meta.value.last_page) return [];
  const pages = [];
  for (let i = 1; i <= meta.value.last_page; i++) {
    pages.push(i);
  }
  return pages;
});

function debounceFetch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    fetchProducts(1);
  }, 350);
}

function selectCategory(slug) {
  if (selectedCategory.value === slug) {
    selectedCategory.value = '';
  } else {
    selectedCategory.value = slug;
  }
  fetchProducts(1);
}

function setPricePreset(min, max) {
  minPrice.value = min !== null ? min : '';
  maxPrice.value = max !== null ? max : '';
  applyPriceFilter();
}

function applyPriceFilter() {
  priceError.value = '';

  const min = minPrice.value !== '' ? parseFloat(minPrice.value) : null;
  const max = maxPrice.value !== '' ? parseFloat(maxPrice.value) : null;

  if (min !== null && max !== null && min > max) {
    priceError.value = 'Min price cannot be greater than max price.';
    return;
  }

  fetchProducts(1);
}

function resetAllFilters() {
  searchQuery.value = '';
  selectedCategory.value = '';
  minPrice.value = '';
  maxPrice.value = '';
  priceError.value = '';
  fetchProducts(1);
}

async function fetchCategories() {
  try {
    const res = await axios.get('/api/categories');
    categories.value = res.data.data;
  } catch (err) {
    console.error('Failed to load categories', err);
  }
}

async function fetchProducts(page = 1) {
  loading.value = true;
  priceError.value = '';

  const params = {
    page,
    per_page: 12
  };

  if (searchQuery.value) params.search = searchQuery.value;
  if (selectedCategory.value) params.category = selectedCategory.value;
  if (minPrice.value !== '') params.min_price = minPrice.value;
  if (maxPrice.value !== '') params.max_price = maxPrice.value;

  try {
    const res = await axios.get('/api/products', { params });
    products.value = res.data.data;
    meta.value = res.data.meta || {};
  } catch (err) {
    if (err.response?.data?.errors?.max_price) {
      priceError.value = err.response.data.errors.max_price[0];
    } else {
      console.error('Failed to load products', err);
    }
  } finally {
    loading.value = false;
  }
}

function formatRupiah(val) {
  if (!val) return 'Rp 0';
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0
  }).format(val);
}

function handleHeroImgError(e) {
  e.target.style.display = 'none';
}

function handleCardImgError(e) {
  e.target.style.display = 'none';
}

onMounted(() => {
  fetchCategories();
  fetchProducts();
});

defineExpose({
  fetchProducts
});
</script>

<style scoped>
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.25s ease-out;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-8px);
  opacity: 0;
}
</style>

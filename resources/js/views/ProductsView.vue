<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-widest text-[#707070] mb-6">
      <router-link to="/" class="hover:text-[#111111] transition-colors">Home</router-link>
      <span>/</span>
      <span class="text-[#111111]">The Collection</span>
    </nav>

    <!-- Page Heading -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 pb-6 border-b border-[#E7E7E5] gap-4">
      <div>
        <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-[#111111]">
          The Collection
        </h1>
        <p class="text-xs sm:text-sm text-[#707070] mt-2 max-w-xl">
          Engineered footwear for everyday movement, sport, and personal expression.
        </p>
      </div>

      <div class="text-xs font-semibold text-[#707070]">
        <span v-if="meta.total !== undefined">{{ meta.total }} styles available</span>
      </div>
    </div>

    <!-- Filters Bar -->
    <ProductFilters
      v-model:search="searchQuery"
      v-model:category="selectedCategory"
      v-model:minPrice="minPrice"
      v-model:maxPrice="maxPrice"
      v-model:sort="sortBy"
      :categories="categories"
      @reset="resetFilters"
    />

    <!-- Error State -->
    <div
      v-if="error"
      class="bg-red-50 border border-red-200 p-8 text-center my-8 max-w-lg mx-auto"
    >
      <p class="text-xs text-red-600 font-semibold mb-4">{{ error }}</p>
      <Button variant="outline" size="sm" @click="fetchProducts">
        Retry Loading
      </Button>
    </div>

    <!-- Loading Skeleton Grid -->
    <div
      v-else-if="loading"
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
    >
      <div v-for="n in 8" :key="n" class="border border-[#E7E7E5] p-4 space-y-4">
        <Skeleton class="w-full aspect-square" />
        <Skeleton class="h-4 w-3/4" />
        <Skeleton class="h-3 w-1/2" />
        <Skeleton class="h-4 w-1/3" />
      </div>
    </div>

    <!-- Empty State -->
    <div
      v-else-if="sortedProducts.length === 0"
      class="border border-[#E7E7E5] p-16 text-center my-12 max-w-md mx-auto space-y-4"
    >
      <div class="text-3xl">👟</div>
      <h3 class="text-base font-bold text-[#111111] uppercase tracking-wider">No Footwear Found</h3>
      <p class="text-xs text-[#707070] leading-relaxed">
        No products matched your active filters. Try modifying your search term or clearing the price parameters.
      </p>
      <Button variant="outline" size="sm" @click="resetFilters">
        Clear All Filters
      </Button>
    </div>

    <!-- Products Grid -->
    <div
      v-else
      class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6"
    >
      <ProductCard
        v-for="product in sortedProducts"
        :key="product.id"
        :product="product"
      />
    </div>

    <!-- Pagination -->
    <div
      v-if="meta.last_page > 1"
      class="mt-14 pt-8 border-t border-[#E7E7E5] flex items-center justify-center gap-2"
    >
      <button
        type="button"
        :disabled="meta.current_page <= 1"
        @click="goToPage(meta.current_page - 1)"
        class="h-9 px-4 border border-[#E7E7E5] text-xs font-semibold uppercase tracking-wider disabled:opacity-30 disabled:pointer-events-none hover:border-black transition-colors"
      >
        ← Prev
      </button>

      <span class="px-4 text-xs font-semibold text-[#707070]">
        Page {{ meta.current_page }} of {{ meta.last_page }}
      </span>

      <button
        type="button"
        :disabled="meta.current_page >= meta.last_page"
        @click="goToPage(meta.current_page + 1)"
        class="h-9 px-4 border border-[#E7E7E5] text-xs font-semibold uppercase tracking-wider disabled:opacity-30 disabled:pointer-events-none hover:border-black transition-colors"
      >
        Next →
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useDebounceFn } from '@vueuse/core';
import type { Product, Category, PaginationMeta } from '@/types';
import productService from '@/services/productService';
import categoryService from '@/services/categoryService';
import ProductCard from '@/components/products/ProductCard.vue';
import ProductFilters from '@/components/products/ProductFilters.vue';
import Skeleton from '@/components/ui/Skeleton.vue';
import Button from '@/components/ui/Button.vue';

const route = useRoute();
const router = useRouter();

const products = ref<Product[]>([]);
const categories = ref<Category[]>([]);
const loading = ref(true);
const error = ref('');

const meta = ref<PaginationMeta>({
  current_page: 1,
  from: null,
  last_page: 1,
  per_page: 12,
  to: null,
  total: 0,
});

// Filters bound to query params
const searchQuery = ref((route.query.search as string) || '');
const selectedCategory = ref((route.query.category as string) || '');
const minPrice = ref((route.query.min_price as string) || '');
const maxPrice = ref((route.query.max_price as string) || '');
const sortBy = ref((route.query.sort as string) || 'recommended');

// Watch for route.query changes (e.g. Navbar links Running, Casual, Navbar Search, Browser back/forward)
watch(
  () => route.query,
  (newQuery) => {
    const qSearch = (newQuery.search as string) || '';
    const qCat = (newQuery.category as string) || '';
    const qMin = (newQuery.min_price as string) || '';
    const qMax = (newQuery.max_price as string) || '';
    const qSort = (newQuery.sort as string) || 'recommended';
    const qPage = Number(newQuery.page) || 1;

    let changed = false;
    if (searchQuery.value !== qSearch) {
      searchQuery.value = qSearch;
      changed = true;
    }
    if (selectedCategory.value !== qCat) {
      selectedCategory.value = qCat;
      changed = true;
    }
    if (minPrice.value !== qMin) {
      minPrice.value = qMin;
      changed = true;
    }
    if (maxPrice.value !== qMax) {
      maxPrice.value = qMax;
      changed = true;
    }
    if (sortBy.value !== qSort) {
      sortBy.value = qSort;
      changed = true;
    }

    fetchProducts(qPage);
  },
  { deep: true }
);

// Debounced search handler for user typing in filter box
const debouncedFetch = useDebounceFn(() => {
  syncQueryParams();
}, 250);

watch(searchQuery, (newVal, oldVal) => {
  if (newVal !== oldVal) debouncedFetch();
});

watch([selectedCategory, minPrice, maxPrice, sortBy], () => {
  syncQueryParams();
});

// Client-side sort on current page dataset
const sortedProducts = computed(() => {
  const list = [...products.value];
  if (sortBy.value === 'price-asc') {
    return list.sort((a, b) => Number(a.price) - Number(b.price));
  }
  if (sortBy.value === 'price-desc') {
    return list.sort((a, b) => Number(b.price) - Number(a.price));
  }
  if (sortBy.value === 'name-asc') {
    return list.sort((a, b) => a.name.localeCompare(b.name));
  }
  return list;
});

function syncQueryParams() {
  const query: Record<string, string> = {};
  if (searchQuery.value) query.search = searchQuery.value;
  if (selectedCategory.value) query.category = selectedCategory.value;
  if (minPrice.value) query.min_price = String(minPrice.value);
  if (maxPrice.value) query.max_price = String(maxPrice.value);
  if (sortBy.value !== 'recommended') query.sort = sortBy.value;

  router.replace({ query }).catch(() => {});
}

function resetFilters() {
  searchQuery.value = '';
  selectedCategory.value = '';
  minPrice.value = '';
  maxPrice.value = '';
  sortBy.value = 'recommended';
  syncQueryParams();
}

function goToPage(page: number) {
  const query = { ...route.query, page: String(page) };
  router.push({ query });
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function fetchCategories() {
  try {
    categories.value = await categoryService.getCategories();
  } catch (err) {
    console.error('Failed to load categories', err);
  }
}

async function fetchProducts(page = 1) {
  loading.value = true;
  error.value = '';

  const params: any = {
    page,
    per_page: 12,
  };

  if (searchQuery.value) params.search = searchQuery.value;
  if (selectedCategory.value) params.category = selectedCategory.value;
  if (minPrice.value) params.min_price = minPrice.value;
  if (maxPrice.value) params.max_price = maxPrice.value;

  try {
    const res = await productService.getProducts(params);
    products.value = res.data;
    meta.value = res.meta;
  } catch (err: any) {
    error.value = err.response?.data?.message || 'Failed to retrieve products from catalog.';
  } finally {
    loading.value = false;
  }
}

onMounted(() => {
  fetchCategories();
  fetchProducts(Number(route.query.page) || 1);
});
</script>

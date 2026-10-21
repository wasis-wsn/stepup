<template>
  <div class="bg-white border border-[#E7E7E5] p-5 sm:p-6 mb-8 space-y-5">
    <!-- Top Row: Search and Sort -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
      <!-- Search Input -->
      <div class="md:col-span-5 relative">
        <input
          :value="search"
          @input="$emit('update:search', ($event.target as HTMLInputElement).value)"
          type="text"
          placeholder="Search footwear by model..."
          class="h-10 w-full border border-[#E7E7E5] bg-[#F5F5F3] px-3.5 pl-9 text-xs text-[#111111] placeholder:text-[#888888] focus:bg-white focus:border-[#111111] focus:outline-none transition-colors"
        />
        <span class="absolute left-3 top-3 text-[#707070] text-xs">🔍</span>
        <button
          v-if="search"
          type="button"
          @click="$emit('update:search', '')"
          class="absolute right-3 top-3 text-xs text-[#707070] hover:text-black"
        >
          ✕
        </button>
      </div>

      <!-- Category Filter Pills -->
      <div class="md:col-span-4 flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
        <button
          type="button"
          @click="$emit('update:category', '')"
          class="h-10 px-3 text-[11px] font-semibold uppercase tracking-wider transition-colors whitespace-nowrap cursor-pointer border"
          :class="!category ? 'bg-[#111111] text-white border-[#111111]' : 'bg-transparent text-[#707070] border-[#E7E7E5] hover:border-black hover:text-black'"
        >
          All
        </button>
        <button
          v-for="cat in categories"
          :key="cat.id"
          type="button"
          @click="$emit('update:category', cat.slug)"
          class="h-10 px-3 text-[11px] font-semibold uppercase tracking-wider transition-colors whitespace-nowrap cursor-pointer border"
          :class="category === cat.slug ? 'bg-[#111111] text-white border-[#111111]' : 'bg-transparent text-[#707070] border-[#E7E7E5] hover:border-black hover:text-black'"
        >
          {{ cat.name }}
        </button>
      </div>

      <!-- Sort Dropdown -->
      <div class="md:col-span-3 flex items-center justify-end gap-2">
        <span class="text-[10px] font-bold uppercase tracking-wider text-[#707070]">Sort:</span>
        <select
          :value="sort"
          @change="$emit('update:sort', ($event.target as HTMLSelectElement).value)"
          class="h-10 border border-[#E7E7E5] bg-white px-3 text-xs text-[#111111] focus:border-[#111111] focus:outline-none cursor-pointer"
        >
          <option value="recommended">Recommended</option>
          <option value="price-asc">Price: Low to High</option>
          <option value="price-desc">Price: High to Low</option>
          <option value="name-asc">Name: A to Z</option>
        </select>
      </div>
    </div>

    <!-- Bottom Row: Price Range & Clear Filters -->
    <div class="pt-4 border-t border-[#F5F5F3] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-3">
        <span class="text-[10px] font-bold uppercase tracking-wider text-[#707070]">Price (IDR):</span>

        <input
          :value="minPrice"
          @input="$emit('update:minPrice', ($event.target as HTMLInputElement).value)"
          type="number"
          placeholder="Min Rp"
          class="h-8 w-24 border border-[#E7E7E5] px-2 text-xs focus:border-[#111111] focus:outline-none"
        />
        <span class="text-[#707070]">—</span>
        <input
          :value="maxPrice"
          @input="$emit('update:maxPrice', ($event.target as HTMLInputElement).value)"
          type="number"
          placeholder="Max Rp"
          class="h-8 w-24 border border-[#E7E7E5] px-2 text-xs focus:border-[#111111] focus:outline-none"
        />

        <!-- Price Presets -->
        <div class="flex items-center gap-1.5 ml-2">
          <button
            type="button"
            @click="setPreset(0, 400000)"
            class="px-2 py-1 bg-[#F5F5F3] hover:bg-[#EAEAE7] text-[10px] font-semibold text-[#707070] hover:text-black transition-colors"
          >
            &lt; 400k
          </button>
          <button
            type="button"
            @click="setPreset(400000, 700000)"
            class="px-2 py-1 bg-[#F5F5F3] hover:bg-[#EAEAE7] text-[10px] font-semibold text-[#707070] hover:text-black transition-colors"
          >
            400k - 700k
          </button>
          <button
            type="button"
            @click="setPreset(700000, '')"
            class="px-2 py-1 bg-[#F5F5F3] hover:bg-[#EAEAE7] text-[10px] font-semibold text-[#707070] hover:text-black transition-colors"
          >
            &gt; 700k
          </button>
        </div>
      </div>

      <button
        v-if="hasActiveFilters"
        type="button"
        @click="$emit('reset')"
        class="text-[10px] font-bold uppercase tracking-wider text-red-600 hover:text-red-700 transition-colors"
      >
        Clear All Filters ✕
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import type { Category } from '@/types';

interface Props {
  search: string;
  category: string;
  minPrice: string | number;
  maxPrice: string | number;
  sort: string;
  categories: Category[];
}

const props = defineProps<Props>();

const emit = defineEmits<{
  (e: 'update:search', val: string): void;
  (e: 'update:category', val: string): void;
  (e: 'update:minPrice', val: string): void;
  (e: 'update:maxPrice', val: string): void;
  (e: 'update:sort', val: string): void;
  (e: 'reset'): void;
}>();

const hasActiveFilters = computed(() => {
  return (
    !!props.search ||
    !!props.category ||
    props.minPrice !== '' ||
    props.maxPrice !== '' ||
    props.sort !== 'recommended'
  );
});

function setPreset(min: number | string, max: number | string) {
  emit('update:minPrice', String(min));
  emit('update:maxPrice', String(max));
}
</script>

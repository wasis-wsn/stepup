<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-[#E7E7E5]">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-[#111111]">
            Product Management
          </h1>
          <Badge variant="dark">Admin Portal</Badge>
        </div>
        <p class="text-xs text-[#707070]">
          Manage catalog styles, update pricing, descriptions, and footwear photography.
        </p>
      </div>

      <router-link to="/admin/products/new">
        <Button size="md" class="w-full sm:w-auto flex items-center gap-2">
          <span>+</span>
          <span>Add New Footwear</span>
        </Button>
      </router-link>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white border border-[#E7E7E5] p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="relative w-full sm:w-80">
        <input
          v-model="searchQuery"
          @input="handleSearchInput"
          type="text"
          placeholder="Filter by name or style code..."
          class="h-9 w-full border border-[#E7E7E5] bg-[#F5F5F3] px-3.5 pl-8 text-xs text-[#111111] focus:bg-white focus:border-black focus:outline-none"
        />
        <span class="absolute left-2.5 top-2.5 text-[#707070] text-xs">🔍</span>
      </div>

      <div class="flex items-center gap-3 w-full sm:w-auto">
        <select
          v-model="selectedCategory"
          @change="fetchProducts(1)"
          class="h-9 border border-[#E7E7E5] bg-white px-3 text-xs text-[#111111] focus:border-black focus:outline-none cursor-pointer w-full sm:w-auto"
        >
          <option value="">All Categories</option>
          <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
            {{ cat.name }}
          </option>
        </select>

        <button
          type="button"
          @click="fetchProducts(meta.current_page)"
          class="h-9 px-3 border border-[#E7E7E5] hover:bg-[#F5F5F3] text-xs text-[#707070] hover:text-black transition-colors"
          title="Refresh"
        >
          ↻
        </button>
      </div>
    </div>

    <!-- Management Data Table -->
    <div class="bg-white border border-[#E7E7E5] overflow-hidden shadow-2xs">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-[#F5F5F3] border-b border-[#E7E7E5] text-[#707070] uppercase tracking-wider font-bold">
            <tr>
              <th class="py-3 px-4 w-16 text-center">ID</th>
              <th class="py-3 px-4">Footwear Model</th>
              <th class="py-3 px-4">Category</th>
              <th class="py-3 px-4">Price (IDR)</th>
              <th class="py-3 px-4">Created Date</th>
              <th class="py-3 px-4 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#E7E7E5]">
            <tr v-if="loading">
              <td colspan="6" class="py-12 text-center text-[#707070]">
                Loading product catalog...
              </td>
            </tr>
            <tr v-else-if="products.length === 0">
              <td colspan="6" class="py-12 text-center text-[#707070]">
                No products found.
              </td>
            </tr>
            <tr
              v-else
              v-for="product in products"
              :key="product.id"
              class="hover:bg-[#FAFAFA] transition-colors"
            >
              <td class="py-3.5 px-4 text-center font-mono text-[#707070]">
                #{{ product.id }}
              </td>
              <td class="py-3.5 px-4">
                <div class="flex items-center gap-3">
                  <div class="w-12 h-12 bg-[#F5F5F3] border border-[#E7E7E5] flex items-center justify-center p-1 shrink-0">
                    <img
                      v-if="product.image"
                      :src="resolveImageUrl(product.image)"
                      :alt="product.name"
                      class="w-full h-full object-contain"
                      @error="handleImgFallback"
                    />
                    <span v-else class="text-xs">👟</span>
                  </div>
                  <div>
                    <p class="font-bold text-[#111111] text-xs">{{ product.name }}</p>
                    <p class="text-[10px] text-[#707070] font-mono">{{ product.slug }}</p>
                  </div>
                </div>
              </td>
              <td class="py-3.5 px-4">
                <Badge variant="default">
                  {{ product.category?.name || 'Footwear' }}
                </Badge>
              </td>
              <td class="py-3.5 px-4 font-bold text-[#111111]">
                {{ formatRupiah(product.price) }}
              </td>
              <td class="py-3.5 px-4 text-[#707070]">
                {{ formatDate(product.created_at) }}
              </td>
              <td class="py-3.5 px-4 text-right">
                <div class="flex items-center justify-end gap-2">
                  <router-link :to="`/admin/products/${product.id}/edit`">
                    <button
                      type="button"
                      class="px-2.5 py-1.5 border border-[#E7E7E5] hover:border-black text-[11px] font-semibold text-[#111111] transition-colors cursor-pointer"
                    >
                      Edit
                    </button>
                  </router-link>
                  <button
                    type="button"
                    @click="initiateDelete(product)"
                    class="px-2.5 py-1.5 border border-red-200 bg-red-50 hover:bg-red-100 text-[11px] font-semibold text-red-600 transition-colors cursor-pointer"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Table Footer / Pagination -->
      <div class="p-4 border-t border-[#E7E7E5] flex items-center justify-between text-xs text-[#707070]">
        <div>
          Total <strong class="text-black">{{ meta.total || 0 }}</strong> products
        </div>

        <div v-if="meta.last_page > 1" class="flex items-center gap-2">
          <button
            type="button"
            :disabled="meta.current_page <= 1"
            @click="fetchProducts(meta.current_page - 1)"
            class="px-3 py-1 border border-[#E7E7E5] text-[11px] font-bold disabled:opacity-30 disabled:pointer-events-none hover:border-black"
          >
            Prev
          </button>
          <span>{{ meta.current_page }} / {{ meta.last_page }}</span>
          <button
            type="button"
            :disabled="meta.current_page >= meta.last_page"
            @click="fetchProducts(meta.current_page + 1)"
            class="px-3 py-1 border border-[#E7E7E5] text-[11px] font-bold disabled:opacity-30 disabled:pointer-events-none hover:border-black"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Accessible Delete Confirmation Dialog -->
    <Dialog v-model:open="showDeleteModal">
      <div class="space-y-4">
        <h3 class="text-base font-bold text-[#111111] uppercase tracking-wider">
          Confirm Footwear Deletion
        </h3>
        <p class="text-xs text-[#707070] leading-relaxed">
          Are you sure you want to permanently delete style
          <strong class="text-black">"{{ productToDelete?.name }}"</strong>?
          This action will immediately remove it from public catalog listings.
        </p>

        <div class="pt-4 flex items-center justify-end gap-3">
          <Button variant="outline" size="sm" @click="showDeleteModal = false">
            Cancel
          </Button>
          <Button
            variant="destructive"
            size="sm"
            :loading="deleting"
            @click="executeDelete"
          >
            Confirm Delete
          </Button>
        </div>
      </div>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useDebounceFn } from '@vueuse/core';
import type { Product, Category, PaginationMeta } from '@/types';
import productService from '@/services/productService';
import categoryService from '@/services/categoryService';
import { formatRupiah, formatDate, resolveImageUrl } from '@/lib/utils';
import { toast } from 'vue-sonner';
import Button from '@/components/ui/Button.vue';
import Badge from '@/components/ui/Badge.vue';
import Dialog from '@/components/ui/Dialog.vue';

const products = ref<Product[]>([]);
const categories = ref<Category[]>([]);
const loading = ref(true);
const deleting = ref(false);

const searchQuery = ref('');
const selectedCategory = ref('');

const meta = ref<PaginationMeta>({
  current_page: 1,
  from: null,
  last_page: 1,
  per_page: 10,
  to: null,
  total: 0,
});

// Delete modal state
const showDeleteModal = ref(false);
const productToDelete = ref<Product | null>(null);

const handleSearchInput = useDebounceFn(() => {
  fetchProducts(1);
}, 300);

async function fetchCategories() {
  try {
    categories.value = await categoryService.getCategories();
  } catch (err) {
    console.error('Failed to load categories', err);
  }
}

async function fetchProducts(page = 1) {
  loading.value = true;
  const params: any = {
    page,
    per_page: 10,
  };
  if (searchQuery.value) params.search = searchQuery.value;
  if (selectedCategory.value) params.category = selectedCategory.value;

  try {
    const res = await productService.getProducts(params);
    products.value = res.data;
    meta.value = res.meta;
  } catch (err) {
    console.error('Failed to load products', err);
  } finally {
    loading.value = false;
  }
}

function initiateDelete(product: Product) {
  productToDelete.value = product;
  showDeleteModal.value = true;
}

async function executeDelete() {
  if (!productToDelete.value) return;

  deleting.value = true;
  try {
    await productService.deleteProduct(productToDelete.value.id);
    toast.success(`"${productToDelete.value.name}" deleted successfully.`);
    showDeleteModal.value = false;
    productToDelete.value = null;
    fetchProducts(meta.value.current_page || 1);
  } catch (err) {
    toast.error('Failed to delete product.');
  } finally {
    deleting.value = false;
  }
}

function handleImgFallback(e: Event) {
  const target = e.target as HTMLImageElement;
  target.style.display = 'none';
}

onMounted(() => {
  fetchCategories();
  fetchProducts();
});
</script>

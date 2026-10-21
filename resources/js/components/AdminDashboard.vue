<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Non-admin Warning / Access Gate -->
    <div
      v-if="!isAdmin"
      class="bg-white rounded-3xl p-10 border border-gray-100 shadow-sm text-center max-w-xl mx-auto my-12"
    >
      <div class="w-16 h-16 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
        🔒
      </div>
      <h2 class="text-xl font-bold text-gray-900 mb-2">Admin Authorization Required</h2>
      <p class="text-sm text-gray-500 mb-6 leading-relaxed">
        Product creation, editing, and deletion are restricted to administrators per the specification (US-04 & US-05).
      </p>
      <button
        @click="$emit('open-auth')"
        class="px-6 py-3 bg-black hover:bg-neutral-800 text-white font-semibold text-xs uppercase tracking-wider rounded-xl transition shadow-sm"
      >
        Sign in as Admin
      </button>
    </div>

    <!-- Admin Content -->
    <div v-else class="space-y-6">
      <!-- Dashboard Header -->
      <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 mb-1">
            <h1 class="text-2xl font-extrabold text-gray-950 tracking-tight">Products Management</h1>
            <span class="badge-tag bg-black text-white text-[10px]">Admin Access</span>
          </div>
          <p class="text-xs text-gray-500">Manage catalog inventory, update pricing, descriptions, and upload shoe imagery.</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
          <button
            @click="openAddModal"
            class="w-full md:w-auto px-5 py-2.5 bg-black hover:bg-neutral-800 text-white rounded-xl text-xs font-semibold tracking-wide transition shadow-sm flex items-center justify-center gap-2"
          >
            <span>+</span>
            <span>Add New Product</span>
          </button>
        </div>
      </div>

      <!-- Filters & Search Bar -->
      <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="relative w-full sm:w-80">
          <input
            v-model="searchQuery"
            @input="debounceFetch"
            type="text"
            placeholder="Search by shoe name..."
            class="w-full pl-9 pr-8 py-2 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-xs"
          />
          <span class="absolute left-3 top-2.5 text-gray-400 text-xs">🔍</span>
          <button
            v-if="searchQuery"
            @click="searchQuery = ''; fetchProducts(1)"
            class="absolute right-2.5 top-2.5 text-gray-400 hover:text-black text-xs"
          >
            ✕
          </button>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
          <select
            v-model="selectedCategory"
            @change="fetchProducts(1)"
            class="w-full sm:w-auto px-3 py-2 rounded-xl border border-gray-200 text-xs focus:outline-none focus:border-black text-gray-700"
          >
            <option value="">All Categories</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.slug">
              {{ cat.name }}
            </option>
          </select>

          <button
            @click="fetchProducts(meta.current_page || 1)"
            class="p-2 border border-gray-200 hover:bg-gray-50 rounded-xl text-xs text-gray-600 transition"
            title="Refresh list"
          >
            🔄
          </button>
        </div>
      </div>

      <!-- Products Table -->
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase tracking-wider font-semibold">
              <tr>
                <th class="py-3.5 px-4 w-12 text-center">ID</th>
                <th class="py-3.5 px-4">Product</th>
                <th class="py-3.5 px-4">Category</th>
                <th class="py-3.5 px-4">Price</th>
                <th class="py-3.5 px-4">Created Date</th>
                <th class="py-3.5 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-if="loading">
                <td colspan="6" class="py-12 text-center text-gray-400">
                  <span class="inline-block animate-spin mr-2">⏳</span> Loading catalog products...
                </td>
              </tr>
              <tr v-else-if="products.length === 0">
                <td colspan="6" class="py-12 text-center text-gray-400">
                  No products found. Click "Add New Product" to create one.
                </td>
              </tr>
              <tr
                v-else
                v-for="product in products"
                :key="product.id"
                class="hover:bg-gray-50/80 transition-colors"
              >
                <td class="py-3.5 px-4 text-center font-mono text-gray-400">#{{ product.id }}</td>
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gray-50 rounded-lg flex items-center justify-center p-1 border border-gray-100 overflow-hidden shrink-0">
                      <img
                        v-if="product.image"
                        :src="product.image"
                        :alt="product.name"
                        class="w-full h-full object-contain"
                        @error="handleImgError"
                      />
                      <span v-else class="text-sm">👟</span>
                    </div>
                    <div>
                      <div class="font-bold text-gray-900 text-sm">{{ product.name }}</div>
                      <div class="text-[11px] text-gray-400 font-mono">{{ product.slug }}</div>
                    </div>
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <span class="badge-tag bg-gray-100 text-gray-700 border border-gray-200">
                    {{ product.category?.name || 'Uncategorized' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 font-bold text-gray-900">
                  {{ formatRupiah(product.price) }}
                </td>
                <td class="py-3.5 px-4 text-gray-500">
                  {{ formatDate(product.created_at) }}
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      @click="openEditModal(product)"
                      class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-semibold text-[11px] transition"
                    >
                      Edit
                    </button>
                    <button
                      @click="confirmDelete(product)"
                      class="px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg font-semibold text-[11px] transition"
                    >
                      Delete
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Table Pagination Footer -->
        <div class="p-4 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
          <div>
            Total <strong class="text-gray-900">{{ meta.total || 0 }}</strong> products
          </div>
          <div v-if="meta.last_page > 1" class="flex items-center gap-1">
            <button
              @click="fetchProducts(meta.current_page - 1)"
              :disabled="meta.current_page <= 1"
              class="px-2.5 py-1 rounded-md border border-gray-200 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed"
            >
              Prev
            </button>
            <span class="px-2">{{ meta.current_page }} / {{ meta.last_page }}</span>
            <button
              @click="fetchProducts(meta.current_page + 1)"
              :disabled="meta.current_page >= meta.last_page"
              class="px-2.5 py-1 rounded-md border border-gray-200 hover:bg-gray-50 disabled:opacity-30 disabled:cursor-not-allowed"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create / Edit Product Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in"
    >
      <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl p-6 border border-gray-100 animate-scale-up max-h-[90vh] overflow-y-auto">
        <button
          @click="closeModal"
          class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 transition"
        >
          ✕
        </button>

        <h2 class="text-lg font-bold text-gray-900 mb-4">
          {{ isEditing ? 'Edit Product' : 'Add New Shoe Product' }}
        </h2>

        <!-- Error Alert -->
        <div v-if="formError" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600">
          {{ formError }}
        </div>

        <form @submit.prevent="submitProduct" class="space-y-4">
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
              Shoe Name <span class="text-red-500">*</span>
            </label>
            <input
              v-model="productForm.name"
              type="text"
              required
              placeholder="e.g. STEPUP Zoom Velocity"
              class="w-full px-3.5 py-2 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                Category <span class="text-red-500">*</span>
              </label>
              <select
                v-model="productForm.category_id"
                required
                class="w-full px-3 py-2 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm text-gray-700"
              >
                <option value="" disabled>Select Category</option>
                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                  {{ cat.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
                Price (IDR) <span class="text-red-500">*</span>
              </label>
              <input
                v-model="productForm.price"
                type="number"
                min="0"
                step="1000"
                required
                placeholder="450000"
                class="w-full px-3.5 py-2 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
              Description
            </label>
            <textarea
              v-model="productForm.description"
              rows="3"
              placeholder="Engineered for responsive cushioning and lightweight durability..."
              class="w-full px-3.5 py-2 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm"
            ></textarea>
          </div>

          <!-- Image Upload & Preview -->
          <div>
            <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">
              Product Image (JPG, PNG, WebP)
            </label>
            <input
              type="file"
              accept="image/*"
              @change="handleFileUpload"
              class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-100 hover:file:bg-gray-200 cursor-pointer"
            />
            <!-- Preview -->
            <div v-if="imagePreview || productForm.current_image" class="mt-3 p-2 bg-gray-50 rounded-xl border border-gray-200 flex items-center gap-3">
              <img
                :src="imagePreview || productForm.current_image"
                alt="Preview"
                class="w-16 h-16 object-contain bg-white rounded-lg p-1 border"
              />
              <span class="text-xs text-gray-500">
                {{ imagePreview ? 'New image selected' : 'Current active image' }}
              </span>
            </div>
          </div>

          <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 text-xs font-semibold transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="saving"
              class="px-5 py-2.5 bg-black hover:bg-neutral-800 text-white rounded-xl text-xs font-semibold tracking-wide transition shadow-sm disabled:opacity-50"
            >
              <span v-if="saving">Saving...</span>
              <span v-else>{{ isEditing ? 'Update Product' : 'Create Product' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div
      v-if="productToDelete"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in"
    >
      <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl p-6 border border-gray-100 animate-scale-up text-center">
        <div class="w-12 h-12 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-3 text-xl">
          🗑️
        </div>
        <h3 class="text-base font-bold text-gray-900 mb-1">Delete Product?</h3>
        <p class="text-xs text-gray-500 mb-6">
          Are you sure you want to remove <strong class="text-gray-900">{{ productToDelete.name }}</strong>? This action cannot be undone.
        </p>

        <div class="flex items-center gap-2">
          <button
            @click="productToDelete = null"
            class="flex-1 py-2.5 rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-semibold transition"
          >
            Cancel
          </button>
          <button
            @click="executeDelete"
            :disabled="deleting"
            class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-semibold transition shadow-sm disabled:opacity-50"
          >
            <span v-if="deleting">Deleting...</span>
            <span v-else>Confirm Delete</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
  isAdmin: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['open-auth', 'toast']);

const products = ref([]);
const categories = ref([]);
const meta = ref({});
const loading = ref(false);
const saving = ref(false);
const deleting = ref(false);

const searchQuery = ref('');
const selectedCategory = ref('');
let debounceTimer = null;

// Modals
const showModal = ref(false);
const isEditing = ref(false);
const formError = ref('');
const imagePreview = ref(null);
const selectedFile = ref(null);
const productToDelete = ref(null);

const productForm = ref({
  id: null,
  name: '',
  category_id: '',
  price: '',
  description: '',
  current_image: null
});

function debounceFetch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    fetchProducts(1);
  }, 350);
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
  const params = {
    page,
    per_page: 10
  };
  if (searchQuery.value) params.search = searchQuery.value;
  if (selectedCategory.value) params.category = selectedCategory.value;

  try {
    const res = await axios.get('/api/products', { params });
    products.value = res.data.data;
    meta.value = res.data.meta || {};
  } catch (err) {
    console.error('Failed to load products', err);
  } finally {
    loading.value = false;
  }
}

function openAddModal() {
  isEditing.value = false;
  formError.value = '';
  selectedFile.value = null;
  imagePreview.value = null;
  productForm.value = {
    id: null,
    name: '',
    category_id: categories.value[0]?.id || '',
    price: '',
    description: '',
    current_image: null
  };
  showModal.value = true;
}

function openEditModal(product) {
  isEditing.value = true;
  formError.value = '';
  selectedFile.value = null;
  imagePreview.value = null;
  productForm.value = {
    id: product.id,
    name: product.name,
    category_id: product.category?.id || '',
    price: parseFloat(product.price),
    description: product.description || '',
    current_image: product.image
  };
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
  selectedFile.value = null;
  imagePreview.value = null;
}

function handleFileUpload(e) {
  const file = e.target.files[0];
  if (file) {
    selectedFile.value = file;
    imagePreview.value = URL.createObjectURL(file);
  }
}

async function submitProduct() {
  saving.value = true;
  formError.value = '';

  const formData = new FormData();
  formData.append('name', productForm.value.name);
  formData.append('category_id', productForm.value.category_id);
  formData.append('price', productForm.value.price);
  if (productForm.value.description) {
    formData.append('description', productForm.value.description);
  }
  if (selectedFile.value) {
    formData.append('image', selectedFile.value);
  }

  try {
    if (isEditing.value) {
      // In Laravel multipart form update, use POST with _method=PUT
      formData.append('_method', 'PUT');
      await axios.post(`/api/products/${productForm.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      emit('toast', { message: 'Product updated successfully!', type: 'success' });
    } else {
      await axios.post('/api/products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      });
      emit('toast', { message: 'New product created successfully!', type: 'success' });
    }

    closeModal();
    fetchProducts(meta.value.current_page || 1);
  } catch (err) {
    if (err.response?.data?.errors) {
      const errList = Object.values(err.response.data.errors).flat();
      formError.value = errList.join(' ');
    } else if (err.response?.data?.message) {
      formError.value = err.response.data.message;
    } else {
      formError.value = 'Failed to save product. Please check your inputs.';
    }
  } finally {
    saving.value = false;
  }
}

function confirmDelete(product) {
  productToDelete.value = product;
}

async function executeDelete() {
  if (!productToDelete.value) return;

  deleting.value = true;
  try {
    await axios.delete(`/api/products/${productToDelete.value.id}`);
    emit('toast', { message: `"${productToDelete.value.name}" deleted.`, type: 'info' });
    productToDelete.value = null;
    fetchProducts(meta.value.current_page || 1);
  } catch (err) {
    emit('toast', { message: 'Failed to delete product.', type: 'error' });
  } finally {
    deleting.value = false;
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

function formatDate(dateStr) {
  if (!dateStr) return '-';
  return new Date(dateStr).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
}

function handleImgError(e) {
  e.target.style.display = 'none';
}

onMounted(() => {
  fetchCategories();
  if (props.isAdmin) {
    fetchProducts();
  }
});

defineExpose({
  fetchProducts
});
</script>

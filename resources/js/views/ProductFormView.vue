<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-[11px] font-semibold uppercase tracking-widest text-[#707070] mb-8">
      <router-link to="/admin/products" class="hover:text-black">Product Management</router-link>
      <span>/</span>
      <span class="text-black">{{ isEditMode ? 'Edit Footwear' : 'Add New Footwear' }}</span>
    </nav>

    <div class="bg-white border border-[#E7E7E5] p-6 sm:p-10 shadow-xs space-y-8">
      <!-- Form Header -->
      <div class="border-b border-[#E7E7E5] pb-6">
        <h1 class="text-2xl font-black text-[#111111] tracking-tight">
          {{ isEditMode ? `Edit "${initialName}"` : 'Add New Footwear Style' }}
        </h1>
        <p class="text-xs text-[#707070] mt-1">
          Specify style details, categorization, pricing, and visual imagery.
        </p>
      </div>

      <!-- General Error Alert -->
      <div
        v-if="generalError"
        class="p-4 bg-red-50 border border-red-200 text-xs text-red-600 font-medium"
      >
        {{ generalError }}
      </div>

      <!-- Form Body -->
      <form @submit.prevent="handleSubmit" class="space-y-6">
        <!-- Shoe Name -->
        <Input
          v-model="form.name"
          label="Model / Style Name"
          placeholder="e.g. STEPUP Zoom Horizon"
          required
          :error="errors.name?.[0]"
        />

        <!-- Category & Price in 2 columns -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div class="space-y-1.5">
            <label class="block text-[11px] font-semibold uppercase tracking-wider text-[#707070]">
              Category <span class="text-black">*</span>
            </label>
            <select
              v-model="form.category_id"
              required
              class="h-10 w-full border border-[#E7E7E5] bg-white px-3.5 text-xs text-[#111111] focus:border-[#111111] focus:outline-none cursor-pointer"
            >
              <option value="" disabled>Select category...</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>
            <p v-if="errors.category_id?.[0]" class="text-[11px] text-red-500 font-medium">
              {{ errors.category_id[0] }}
            </p>
          </div>

          <Input
            v-model="form.price"
            label="Price (IDR)"
            type="number"
            min="0"
            step="1000"
            placeholder="450000"
            required
            :error="errors.price?.[0]"
          />
        </div>

        <!-- Description -->
        <div class="space-y-1.5">
          <label class="block text-[11px] font-semibold uppercase tracking-wider text-[#707070]">
            Editorial Description
          </label>
          <textarea
            v-model="form.description"
            rows="4"
            placeholder="Engineered with breathable mesh upper and dual-density foam cushioning..."
            class="w-full border border-[#E7E7E5] p-3 text-xs text-[#111111] placeholder:text-[#999999] focus:border-black focus:outline-none transition-colors"
          ></textarea>
          <p v-if="errors.description?.[0]" class="text-[11px] text-red-500 font-medium">
            {{ errors.description[0] }}
          </p>
        </div>

        <!-- Footwear Photography Upload -->
        <div class="space-y-2 pt-2 border-t border-[#F5F5F3]">
          <label class="block text-[11px] font-semibold uppercase tracking-wider text-[#707070]">
            Product Image (JPEG, PNG, WebP)
          </label>
          <input
            type="file"
            accept="image/jpeg,image/png,image/jpg,image/webp"
            @change="handleFileChange"
            class="text-xs text-[#707070] file:mr-4 file:py-2 file:px-4 file:border file:border-[#E7E7E5] file:text-xs file:font-semibold file:uppercase file:tracking-wider file:bg-[#F5F5F3] hover:file:bg-[#EAEAE7] file:cursor-pointer cursor-pointer"
          />
          <p v-if="errors.image?.[0]" class="text-[11px] text-red-500 font-medium">
            {{ errors.image[0] }}
          </p>

          <!-- Image Preview -->
          <div
            v-if="imagePreview || currentImageUrl"
            class="mt-4 p-4 bg-[#F5F5F3] border border-[#E7E7E5] flex items-center gap-4"
          >
            <div class="w-20 h-20 bg-white border border-[#E7E7E5] p-1 flex items-center justify-center shrink-0">
              <img
                :src="imagePreview || resolveImageUrl(currentImageUrl)"
                alt="Shoe Preview"
                class="w-full h-full object-contain"
              />
            </div>
            <div class="text-xs text-[#707070]">
              <p class="font-bold text-black">
                {{ imagePreview ? 'Selected new image file' : 'Current active photography' }}
              </p>
              <p class="text-[11px]">Recommended: Clean footwear on plain white background.</p>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="pt-6 border-t border-[#E7E7E5] flex items-center justify-end gap-3">
          <router-link to="/admin/products">
            <Button variant="outline" size="md">
              Cancel
            </Button>
          </router-link>
          <Button
            type="submit"
            size="md"
            :loading="saving"
          >
            {{ isEditMode ? 'Save Changes' : 'Publish Footwear Style' }}
          </Button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import type { Category } from '@/types';
import productService from '@/services/productService';
import categoryService from '@/services/categoryService';
import { resolveImageUrl } from '@/lib/utils';
import { toast } from 'vue-sonner';
import Input from '@/components/ui/Input.vue';
import Button from '@/components/ui/Button.vue';

const route = useRoute();
const router = useRouter();

const productId = computed(() => route.params.id as string | undefined);
const isEditMode = computed(() => !!productId.value);

const initialName = ref('');
const categories = ref<Category[]>([]);
const saving = ref(false);
const generalError = ref('');
const errors = ref<Record<string, string[]>>({});

const selectedFile = ref<File | null>(null);
const imagePreview = ref<string | null>(null);
const currentImageUrl = ref<string | null>(null);

const form = ref({
  name: '',
  category_id: '' as string | number,
  price: '' as string | number,
  description: '',
});

function handleFileChange(e: Event) {
  const target = e.target as HTMLInputElement;
  if (target.files && target.files[0]) {
    selectedFile.value = target.files[0];
    imagePreview.value = URL.createObjectURL(target.files[0]);
  }
}

async function loadCategories() {
  try {
    categories.value = await categoryService.getCategories();
  } catch (err) {
    console.error('Failed to load categories', err);
  }
}

async function loadExistingProduct() {
  if (!productId.value) return;

  try {
    const item = await productService.getProductById(productId.value);
    initialName.value = item.name;
    form.value.name = item.name;
    form.value.category_id = item.category?.id || '';
    form.value.price = parseFloat(String(item.price));
    form.value.description = item.description || '';
    currentImageUrl.value = item.image;
  } catch (err) {
    toast.error('Failed to load product details.');
    router.push('/admin/products');
  }
}

async function handleSubmit() {
  saving.value = true;
  generalError.value = '';
  errors.value = {};

  try {
    if (isEditMode.value) {
      await productService.updateProduct(productId.value!, {
        name: form.value.name,
        category_id: form.value.category_id,
        price: form.value.price,
        description: form.value.description,
        image: selectedFile.value || undefined,
      });
      toast.success('Footwear style updated successfully.');
    } else {
      await productService.createProduct({
        name: form.value.name,
        category_id: form.value.category_id,
        price: form.value.price,
        description: form.value.description,
        image: selectedFile.value,
      });
      toast.success('New footwear style published to catalog.');
    }

    router.push('/admin/products');
  } catch (err: any) {
    if (err.response?.data?.errors) {
      errors.value = err.response.data.errors;
    } else if (err.response?.data?.message) {
      generalError.value = err.response.data.message;
    } else {
      generalError.value = 'Failed to submit form. Please check your inputs.';
    }
    toast.error(generalError.value || 'Validation error');
  } finally {
    saving.value = false;
  }
}

onMounted(async () => {
  await loadCategories();
  if (isEditMode.value) {
    await loadExistingProduct();
  } else if (categories.value.length > 0) {
    form.value.category_id = categories.value[0].id;
  }
});
</script>

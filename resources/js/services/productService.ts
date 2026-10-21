import apiClient from '@/lib/api';
import type { Product, PaginatedResponse, ProductPayload } from '@/types';
import { mockProducts } from './mockData';

export interface ProductFilterParams {
  search?: string;
  category?: string;
  min_price?: number | string;
  max_price?: number | string;
  page?: number;
  per_page?: number;
}

// In-memory response cache to ensure instantaneous UI filter responses
const productsCache = new Map<string, { data: PaginatedResponse<Product>; timestamp: number }>();
const CACHE_TTL_MS = 60 * 1000; // 60 seconds

export const productService = {
  clearCache() {
    productsCache.clear();
  },

  async getProducts(params?: ProductFilterParams, useCache = true): Promise<PaginatedResponse<Product>> {
    const cacheKey = JSON.stringify(params || {});
    if (useCache && productsCache.has(cacheKey)) {
      const cached = productsCache.get(cacheKey)!;
      if (Date.now() - cached.timestamp < CACHE_TTL_MS) {
        return cached.data;
      }
    }

    try {
      const response = await apiClient.get<PaginatedResponse<Product>>('/products', { params });
      productsCache.set(cacheKey, { data: response.data, timestamp: Date.now() });
      return response.data;
    } catch (err) {
      console.warn('API products unavailable, falling back to mock dataset', err);
      // Construct a mock paginated response
      let filtered = [...mockProducts];
      if (params?.search) {
        const q = params.search.toLowerCase();
        filtered = filtered.filter(p => p.name.toLowerCase().includes(q));
      }
      if (params?.category) {
        filtered = filtered.filter(p => p.category?.slug === params.category);
      }
      return {
        data: filtered,
        links: { first: null, last: null, prev: null, next: null },
        meta: {
          current_page: 1,
          from: 1,
          last_page: 1,
          per_page: filtered.length,
          to: filtered.length,
          total: filtered.length,
        },
      };
    }
  },

  async getProductById(id: number | string): Promise<Product> {
    try {
      const response = await apiClient.get<{ data: Product }>(`/products/${id}`);
      return response.data.data;
    } catch (err) {
      const found = mockProducts.find(p => p.id === Number(id));
      if (found) return found;
      throw err;
    }
  },

  async createProduct(payload: ProductPayload): Promise<Product> {
    const formData = new FormData();
    formData.append('name', payload.name);
    formData.append('category_id', String(payload.category_id));
    formData.append('price', String(payload.price));
    if (payload.description !== undefined && payload.description !== null) {
      formData.append('description', payload.description);
    }
    if (payload.detail !== undefined && payload.detail !== null) {
      formData.append('detail', payload.detail);
    }
    if (payload.sizes !== undefined && payload.sizes !== null) {
      const sizesVal = Array.isArray(payload.sizes) ? payload.sizes.join(', ') : payload.sizes;
      formData.append('sizes', sizesVal);
    }
    if (payload.color !== undefined && payload.color !== null) {
      formData.append('color', payload.color);
    }
    if (payload.discount !== undefined && payload.discount !== null) {
      formData.append('discount', String(payload.discount));
    }
    if (payload.image instanceof File) {
      formData.append('image', payload.image);
    } else if (typeof payload.image === 'string' && payload.image) {
      formData.append('image', payload.image);
    }

    const response = await apiClient.post<{ data: Product }>('/products', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    productsCache.clear();
    return response.data.data;
  },

  async updateProduct(id: number | string, payload: Partial<ProductPayload>): Promise<Product> {
    const formData = new FormData();
    formData.append('_method', 'PUT');

    if (payload.name !== undefined) formData.append('name', payload.name);
    if (payload.category_id !== undefined) formData.append('category_id', String(payload.category_id));
    if (payload.price !== undefined) formData.append('price', String(payload.price));
    if (payload.description !== undefined) formData.append('description', payload.description || '');
    if (payload.detail !== undefined) formData.append('detail', payload.detail || '');
    if (payload.sizes !== undefined) {
      const sizesVal = Array.isArray(payload.sizes) ? payload.sizes.join(', ') : (payload.sizes || '');
      formData.append('sizes', sizesVal);
    }
    if (payload.color !== undefined) formData.append('color', payload.color || '');
    if (payload.discount !== undefined) formData.append('discount', String(payload.discount || 0));

    if (payload.image instanceof File) {
      formData.append('image', payload.image);
    }

    const response = await apiClient.post<{ data: Product }>(`/products/${id}`, formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    productsCache.clear();
    return response.data.data;
  },

  async deleteProduct(id: number | string): Promise<void> {
    await apiClient.delete(`/products/${id}`);
    productsCache.clear();
  },
};

export default productService;

import apiClient from '@/lib/api';
import type { Category } from '@/types';
import { mockCategories } from './mockData';

export const categoryService = {
  async getCategories(): Promise<Category[]> {
    try {
      const response = await apiClient.get<{ data: Category[] }>('/categories');
      return response.data.data;
    } catch (err) {
      console.warn('API categories unavailable, using mock fallback', err);
      return mockCategories;
    }
  },
};

export default categoryService;

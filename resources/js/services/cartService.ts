import apiClient from '@/lib/api';
import type { Product } from '@/types';

export interface ApiCartItem {
  id: number;
  user_id: number;
  product_id: number;
  size: string;
  quantity: number;
  product: Product;
  created_at?: string;
  updated_at?: string;
}

export const cartService = {
  async getCart(): Promise<ApiCartItem[]> {
    const res = await apiClient.get<{ data: ApiCartItem[] }>('/cart');
    return res.data.data;
  },

  async addToCart(productId: number, size: string, quantity = 1): Promise<ApiCartItem> {
    const res = await apiClient.post<{ data: ApiCartItem }>('/cart', {
      product_id: productId,
      size,
      quantity,
    });
    return res.data.data;
  },

  async updateQuantity(cartItemId: number, quantity: number): Promise<ApiCartItem> {
    const res = await apiClient.put<{ data: ApiCartItem }>(`/cart/${cartItemId}`, {
      quantity,
    });
    return res.data.data;
  },

  async removeItem(cartItemId: number): Promise<void> {
    await apiClient.delete(`/cart/${cartItemId}`);
  },

  async clearCart(): Promise<void> {
    await apiClient.delete('/cart');
  },

  async syncCart(items: Array<{ product_id: number; size: string; quantity: number }>): Promise<ApiCartItem[]> {
    const res = await apiClient.post<{ data: ApiCartItem[] }>('/cart/sync', { items });
    return res.data.data;
  },
};

export default cartService;

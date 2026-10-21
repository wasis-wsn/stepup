import { defineStore } from 'pinia';
import { ref, computed, watch } from 'vue';
import type { Product } from '@/types';
import { useAuthStore } from './auth';
import cartService, { type ApiCartItem } from '@/services/cartService';
import { toast } from 'vue-sonner';

export interface CartItem {
  id: string; // unique client key: `${product.id}-${size}`
  dbId?: number; // MySQL database ID for regular users
  product: Product;
  quantity: number;
  size: string;
}

const STORAGE_KEY = 'stepup_cart';

export const useCartStore = defineStore('cart', () => {
  const items = ref<CartItem[]>([]);
  const isOpen = ref(false);
  const loading = ref(false);

  const auth = useAuthStore();

  // Load initial items from LocalStorage for guest or offline fallback
  try {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved) {
      items.value = JSON.parse(saved);
    }
  } catch (err) {
    console.error('Failed to parse cart storage', err);
    items.value = [];
  }

  // Persist to LocalStorage whenever items change
  watch(
    items,
    (val) => {
      try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(val));
      } catch (err) {
        console.error('Failed to save cart storage', err);
      }
    },
    { deep: true }
  );

  // When user authentication status changes (login / logout)
  watch(
    () => auth.isAuthenticated,
    async (isAuthenticated) => {
      if (isAuthenticated) {
        await syncAndFetchDatabaseCart();
      }
    },
    { immediate: true }
  );

  const totalItems = computed(() =>
    items.value.reduce((total, item) => total + item.quantity, 0)
  );

  const subtotal = computed(() =>
    items.value.reduce((total, item) => total + Number(item.product.price) * item.quantity, 0)
  );

  /**
   * Fetch user cart from MySQL database.
   */
  async function fetchDatabaseCart() {
    if (!auth.isAuthenticated) return;
    loading.value = true;
    try {
      const dbItems = await cartService.getCart();
      items.value = dbItems.map((dbItem) => ({
        id: `${dbItem.product_id}-${dbItem.size}`,
        dbId: dbItem.id,
        product: dbItem.product,
        quantity: dbItem.quantity,
        size: dbItem.size,
      }));
    } catch (err) {
      console.error('Failed to fetch user cart from database', err);
    } finally {
      loading.value = false;
    }
  }

  /**
   * Sync guest localStorage cart into database upon login, then load merged cart.
   */
  async function syncAndFetchDatabaseCart() {
    if (!auth.isAuthenticated) return;

    if (items.value.length > 0) {
      const guestItems = items.value.map((i) => ({
        product_id: i.product.id,
        size: i.size,
        quantity: i.quantity,
      }));

      try {
        const synced = await cartService.syncCart(guestItems);
        items.value = synced.map((dbItem) => ({
          id: `${dbItem.product_id}-${dbItem.size}`,
          dbId: dbItem.id,
          product: dbItem.product,
          quantity: dbItem.quantity,
          size: dbItem.size,
        }));
        return;
      } catch (err) {
        console.warn('Cart sync failed, loading server cart', err);
      }
    }

    await fetchDatabaseCart();
  }

  /**
   * Add a product to cart (stored in database for regular users, localStorage for guests).
   */
  async function addItem(product: Product, size = '42', quantity = 1) {
    const cartItemId = `${product.id}-${size}`;
    const existing = items.value.find((i) => i.id === cartItemId);

    if (existing) {
      existing.quantity += quantity;
    } else {
      items.value.push({
        id: cartItemId,
        product,
        quantity,
        size,
      });
    }

    toast.success(`Added ${product.name} (EU ${size}) to bag.`, {
      description: auth.isAuthenticated ? 'Saved to your account cart.' : 'Your bag has been updated.',
    });
    isOpen.value = true;

    // Persist to database if authenticated regular user
    if (auth.isAuthenticated) {
      try {
        const dbItem = await cartService.addToCart(product.id, size, quantity);
        const itemInList = items.value.find((i) => i.id === cartItemId);
        if (itemInList) {
          itemInList.dbId = dbItem.id;
          itemInList.quantity = dbItem.quantity;
        }
      } catch (err) {
        console.error('Failed to persist cart item to database', err);
      }
    }
  }

  /**
   * Update quantity of an item.
   */
  async function updateQuantity(cartItemId: string, delta: number) {
    const index = items.value.findIndex((i) => i.id === cartItemId);
    if (index === -1) return;

    const item = items.value[index];
    const newQty = item.quantity + delta;

    if (newQty <= 0) {
      await removeItem(cartItemId);
      return;
    }

    item.quantity = newQty;

    // Persist update to database if authenticated regular user
    if (auth.isAuthenticated && item.dbId) {
      try {
        await cartService.updateQuantity(item.dbId, newQty);
      } catch (err) {
        console.error('Failed to update cart quantity in database', err);
      }
    }
  }

  /**
   * Remove a specific item from cart.
   */
  async function removeItem(cartItemId: string) {
    const item = items.value.find((i) => i.id === cartItemId);
    items.value = items.value.filter((i) => i.id !== cartItemId);

    if (item) {
      toast.info(`Removed ${item.product.name} from bag.`);

      // Persist delete to database if authenticated regular user
      if (auth.isAuthenticated && item.dbId) {
        try {
          await cartService.removeItem(item.dbId);
        } catch (err) {
          console.error('Failed to remove cart item from database', err);
        }
      }
    }
  }

  /**
   * Clear all items from cart.
   */
  async function clearCart() {
    items.value = [];
    localStorage.removeItem(STORAGE_KEY);

    if (auth.isAuthenticated) {
      try {
        await cartService.clearCart();
      } catch (err) {
        console.error('Failed to clear cart in database', err);
      }
    }
  }

  function openCart() {
    isOpen.value = true;
  }

  function closeCart() {
    isOpen.value = false;
  }

  function toggleCart() {
    isOpen.value = !isOpen.value;
  }

  return {
    items,
    isOpen,
    loading,
    totalItems,
    subtotal,
    fetchDatabaseCart,
    syncAndFetchDatabaseCart,
    addItem,
    updateQuantity,
    removeItem,
    clearCart,
    openCart,
    closeCart,
    toggleCart,
  };
});

export default useCartStore;

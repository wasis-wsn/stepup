<template>
  <div>
    <!-- Backdrop Overlay -->
    <transition
      enter-active-class="transition-opacity duration-300 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition-opacity duration-200 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="cart.isOpen"
        class="fixed inset-0 bg-black/50 z-50 backdrop-blur-xs"
        @click="cart.closeCart"
        aria-hidden="true"
      />
    </transition>

    <!-- Slide-over Drawer Panel -->
    <transition
      enter-active-class="transform transition duration-300 ease-out"
      enter-from-class="translate-x-full"
      enter-to-class="translate-x-0"
      leave-active-class="transform transition duration-200 ease-in"
      leave-from-class="translate-x-0"
      leave-to-class="translate-x-full"
    >
      <div
        v-if="cart.isOpen"
        class="fixed inset-y-0 right-0 max-w-full flex pl-10 z-50"
      >
        <div class="w-screen max-w-md bg-white border-l border-[#E7E7E5] flex flex-col shadow-2xl">
          <!-- Header -->
          <div class="p-6 border-b border-[#E7E7E5] flex items-center justify-between">
            <div class="flex items-center gap-2">
              <ShoppingBag class="w-4 h-4 text-[#111111]" />
              <h2 class="text-xs font-bold uppercase tracking-widest text-[#111111]">
                Shopping Bag ({{ cart.totalItems }})
              </h2>
            </div>
            <button
              type="button"
              @click="cart.closeCart"
              class="text-[#707070] hover:text-[#111111] transition-colors p-1"
              aria-label="Close cart"
            >
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Empty State -->
          <div
            v-if="cart.items.length === 0"
            class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-4"
          >
            <div class="w-16 h-16 rounded-full bg-[#F5F5F3] flex items-center justify-center text-2xl">
              👟
            </div>
            <h3 class="text-sm font-bold uppercase tracking-wider text-[#111111]">
              Your Bag is Empty
            </h3>
            <p class="text-xs text-[#707070] max-w-xs leading-relaxed">
              Explore our curated catalog of minimalist runners, casual sneakers, and leather footwear.
            </p>
            <router-link
              to="/products"
              @click="cart.closeCart"
              class="inline-block px-6 py-3 bg-[#111111] text-white text-xs font-bold uppercase tracking-widest hover:bg-black transition-colors"
            >
              Explore Collection
            </router-link>
          </div>

          <!-- Items List -->
          <div v-else class="flex-1 overflow-y-auto p-6 space-y-6 divide-y divide-[#F5F5F3]">
            <div
              v-for="item in cart.items"
              :key="item.id"
              class="pt-6 first:pt-0 flex gap-4"
            >
              <!-- Thumbnail -->
              <router-link
                :to="`/products/${item.product.id}`"
                @click="cart.closeCart"
                class="w-20 h-20 bg-[#F5F5F3] border border-[#E7E7E5] shrink-0 overflow-hidden flex items-center justify-center"
              >
                <img
                  :src="getImageUrl(item.product.image)"
                  :alt="item.product.name"
                  class="w-full h-full object-cover object-center"
                  loading="lazy"
                />
              </router-link>

              <!-- Info -->
              <div class="flex-1 min-w-0 flex flex-col justify-between">
                <div>
                  <div class="flex items-start justify-between gap-2">
                    <router-link
                      :to="`/products/${item.product.id}`"
                      @click="cart.closeCart"
                      class="text-xs font-bold text-[#111111] hover:text-[#707070] transition-colors truncate"
                    >
                      {{ item.product.name }}
                    </router-link>
                    <button
                      type="button"
                      @click="cart.removeItem(item.id)"
                      class="text-[#707070] hover:text-red-600 transition-colors p-0.5"
                      aria-label="Remove item"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>

                  <p class="text-[10px] text-[#707070] uppercase tracking-wider mt-0.5">
                    {{ item.product.category?.name || 'Footwear' }} &bull; Size EU {{ item.size }}
                  </p>
                </div>

                <div class="flex items-center justify-between mt-3">
                  <!-- Quantity Stepper -->
                  <div class="flex items-center border border-[#E7E7E5] bg-[#F5F5F3]">
                    <button
                      type="button"
                      @click="cart.updateQuantity(item.id, -1)"
                      class="w-7 h-7 flex items-center justify-center text-xs font-bold text-[#707070] hover:text-black hover:bg-white transition-colors"
                      aria-label="Decrease quantity"
                    >
                      &minus;
                    </button>
                    <span class="w-7 text-center text-xs font-semibold text-[#111111]">
                      {{ item.quantity }}
                    </span>
                    <button
                      type="button"
                      @click="cart.updateQuantity(item.id, 1)"
                      class="w-7 h-7 flex items-center justify-center text-xs font-bold text-[#707070] hover:text-black hover:bg-white transition-colors"
                      aria-label="Increase quantity"
                    >
                      &#43;
                    </button>
                  </div>

                  <!-- Price -->
                  <p class="text-xs font-bold text-[#111111]">
                    {{ formatCurrency(Number(item.product.price) * item.quantity) }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer / Checkout Section -->
          <div v-if="cart.items.length > 0" class="p-6 border-t border-[#E7E7E5] bg-[#FAFAF8] space-y-4">
            <div class="space-y-1.5 text-xs">
              <div class="flex items-center justify-between text-[#707070]">
                <span>Subtotal</span>
                <span>{{ formatCurrency(cart.subtotal) }}</span>
              </div>
              <div class="flex items-center justify-between text-[#707070]">
                <span>Shipping (Standard)</span>
                <span class="text-emerald-600 font-semibold uppercase text-[11px]">Free</span>
              </div>
              <div class="pt-2 border-t border-[#E7E7E5] flex items-center justify-between text-sm font-bold text-[#111111]">
                <span>Total</span>
                <span>{{ formatCurrency(cart.subtotal) }}</span>
              </div>
            </div>

            <!-- Logged-in Regular User Checkout -->
            <div v-if="auth.isAuthenticated" class="space-y-2">
              <button
                type="button"
                @click="handleUserCheckout"
                class="w-full py-3.5 bg-[#111111] text-white text-xs font-bold uppercase tracking-widest hover:bg-black transition-colors flex items-center justify-center gap-2"
              >
                <span>Proceed to Checkout</span>
                <span>&rarr;</span>
              </button>
              <p class="text-[10px] text-center text-[#707070]">
                Logged in as <strong>{{ auth.user?.name }}</strong> ({{ auth.user?.role }})
              </p>
            </div>

            <!-- Guest User: Flexible checkout without forced register -->
            <div v-else class="space-y-2">
              <button
                type="button"
                @click="handleGuestCheckout"
                class="w-full py-3.5 bg-[#111111] text-white text-xs font-bold uppercase tracking-widest hover:bg-black transition-colors flex items-center justify-center gap-2"
              >
                <span>Checkout as Guest</span>
                <span>&rarr;</span>
              </button>
              <router-link
                to="/login"
                @click="cart.closeCart"
                class="block w-full py-2.5 text-center border border-[#E7E7E5] text-[#111111] text-xs font-semibold uppercase tracking-wider hover:border-black transition-colors bg-white"
              >
                Sign In to Account
              </router-link>
              <p class="text-[10px] text-center text-[#707070]">
                No registration required. You can complete your order directly.
              </p>
            </div>
          </div>
        </div>
      </div>
    </transition>

    <!-- Demo Checkout Modal Confirmation -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 scale-95"
      enter-to-class="opacity-100 scale-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 scale-100"
      leave-to-class="opacity-0 scale-95"
    >
      <div
        v-if="checkoutSuccessModal"
        class="fixed inset-0 z-60 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
      >
        <div class="bg-white max-w-sm w-full p-8 text-center space-y-4 border border-[#E7E7E5] shadow-2xl">
          <div class="w-14 h-14 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl font-bold">
            ✓
          </div>
          <h3 class="text-base font-black tracking-tight uppercase text-[#111111]">
            Order Confirmed
          </h3>
          <p class="text-xs text-[#707070] leading-relaxed">
            Thank you for shopping with <strong>STEPUP</strong>! Your order with {{ completedItemCount }} item(s) totalling <strong>{{ formatCurrency(completedTotal) }}</strong> has been placed.
          </p>
          <div class="pt-2">
            <button
              type="button"
              @click="closeCheckoutModal"
              class="w-full py-3 bg-[#111111] text-white text-xs font-bold uppercase tracking-widest hover:bg-black transition-colors"
            >
              Continue Shopping
            </button>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { ShoppingBag, X, Trash2 } from 'lucide-vue-next';
import { useCartStore } from '@/stores/cart';
import { useAuthStore } from '@/stores/auth';
import { formatCurrency, getImageUrl } from '@/lib/utils';
import { toast } from 'vue-sonner';

const cart = useCartStore();
const auth = useAuthStore();

const checkoutSuccessModal = ref(false);
const completedTotal = ref(0);
const completedItemCount = ref(0);

function handleUserCheckout() {
  completedTotal.value = cart.subtotal;
  completedItemCount.value = cart.totalItems;
  cart.clearCart();
  cart.closeCart();
  checkoutSuccessModal.value = true;
  toast.success('Order completed successfully!');
}

function handleGuestCheckout() {
  completedTotal.value = cart.subtotal;
  completedItemCount.value = cart.totalItems;
  cart.clearCart();
  cart.closeCart();
  checkoutSuccessModal.value = true;
  toast.success('Guest order placed successfully!');
}

function closeCheckoutModal() {
  checkoutSuccessModal.value = false;
}
</script>

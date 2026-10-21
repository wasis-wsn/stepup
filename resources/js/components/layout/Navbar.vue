<template>
  <header
    :class="
      cn(
        'sticky top-0 z-40 w-full bg-white transition-all duration-300',
        y > 20 ? 'border-b border-[#E7E7E5] py-3.5 shadow-2xs' : 'py-5 border-b border-transparent'
      )
    "
  >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
      <!-- Left: Brand Wordmark -->
      <div class="flex items-center gap-10">
        <router-link to="/" class="flex items-center gap-2 group">
          <span class="text-xl sm:text-2xl font-black tracking-tighter text-[#111111] uppercase select-none">
            STEPUP
          </span>
          <span class="hidden md:inline-block text-[10px] uppercase tracking-widest font-semibold text-[#707070] pl-2 border-l border-[#E7E7E5]">
            Footwear
          </span>
        </router-link>

        <!-- Desktop Navigation Links -->
        <nav class="hidden md:flex items-center gap-8 text-xs font-semibold uppercase tracking-wider text-[#707070]">
          <router-link
            to="/"
            class="hover:text-[#111111] transition-colors py-1 relative"
            :class="$route.path === '/' && 'text-[#111111] after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-black'"
          >
            Home
          </router-link>
          <router-link
            to="/products"
            class="hover:text-[#111111] transition-colors py-1 relative"
            :class="$route.path.startsWith('/products') && 'text-[#111111] after:absolute after:bottom-0 after:left-0 after:right-0 after:h-0.5 after:bg-black'"
          >
            Shop
          </router-link>
          <router-link
            to="/products?category=running-shoes"
            class="hover:text-[#111111] transition-colors py-1"
          >
            Running
          </router-link>
          <router-link
            to="/products?category=casual-sneakers"
            class="hover:text-[#111111] transition-colors py-1"
          >
            Casual
          </router-link>
          <a
            href="/api/documentation"
            target="_blank"
            rel="noopener noreferrer"
            class="hover:text-[#111111] transition-colors py-1 flex items-center gap-1 font-semibold"
            title="Interactive Swagger API Documentation"
          >
            <span>API Docs</span>
            <ExternalLink class="w-3 h-3 opacity-60" />
          </a>
          <router-link
            v-if="auth.isAdmin"
            to="/admin/products"
            class="text-[#111111] bg-[#F5F5F3] px-2.5 py-1 border border-[#E7E7E5] hover:border-[#111111] transition-all flex items-center gap-1.5"
          >
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Admin</span>
          </router-link>
        </nav>
      </div>

      <!-- Right: Action Icons & Account -->
      <div class="flex items-center gap-4 sm:gap-6">
        <!-- Quick Search Toggle / Input -->
        <div class="relative hidden sm:flex items-center">
          <input
            v-if="searchOpen"
            ref="searchInputRef"
            v-model="searchQuery"
            @keydown.enter="handleSearch"
            type="text"
            placeholder="Search footwear..."
            class="h-8 w-48 border-b border-black text-xs text-[#111111] focus:outline-none bg-transparent pr-7 pl-1"
          />
          <button
            type="button"
            @click="onSearchButtonClick"
            class="text-[#707070] hover:text-[#111111] transition-colors p-1"
            aria-label="Search collection"
          >
            <Search class="w-4 h-4" />
          </button>
        </div>

        <!-- Account / Login -->
        <div v-if="auth.isAuthenticated" class="relative">
          <div class="flex items-center gap-3">
            <button
              @click="userMenuOpen = !userMenuOpen"
              class="flex items-center gap-2 text-xs font-semibold text-[#111111] hover:text-neutral-600 transition-colors"
            >
              <UserIcon class="w-4 h-4" />
              <span class="hidden lg:inline-block max-w-[120px] truncate">{{ auth.user?.name }}</span>
            </button>

            <!-- User Menu Dropdown -->
            <div
              v-if="userMenuOpen"
              class="absolute right-0 top-full mt-2 w-48 bg-white border border-[#E7E7E5] shadow-lg py-2 z-50 text-xs"
              @click="userMenuOpen = false"
            >
              <div class="px-3 py-1.5 border-b border-[#E7E7E5] text-[10px] text-[#707070] uppercase font-bold tracking-wider">
                Signed in as {{ auth.user?.role }}
              </div>
              <router-link
                v-if="auth.isAdmin"
                to="/admin/products"
                class="block px-3 py-2 text-[#111111] hover:bg-[#F5F5F3] font-medium"
              >
                Product Management
              </router-link>
              <button
                @click="handleLogout"
                class="w-full text-left px-3 py-2 text-[#707070] hover:text-black hover:bg-[#F5F5F3] font-medium"
              >
                Sign Out
              </button>
            </div>
          </div>
        </div>

        <router-link
          v-else
          to="/login"
          class="text-xs font-semibold uppercase tracking-wider text-[#707070] hover:text-[#111111] transition-colors"
        >
          Sign In
        </router-link>

        <!-- Interactive Shopping Bag Icon with Cart Drawer trigger -->
        <button
          type="button"
          @click="cart.toggleCart"
          class="text-[#111111] hover:text-[#707070] transition-colors p-1.5 relative cursor-pointer"
          aria-label="Open shopping bag"
          title="Shopping Bag"
        >
          <ShoppingBag class="w-4 h-4" />
          <span
            v-if="cart.totalItems > 0"
            class="absolute -top-1 -right-1 min-w-[16px] h-4 px-1 rounded-full bg-black text-white text-[9px] font-bold flex items-center justify-center leading-none"
          >
            {{ cart.totalItems }}
          </span>
        </button>

        <!-- Mobile Menu Toggle Button -->
        <button
          type="button"
          @click="mobileMenuOpen = !mobileMenuOpen"
          class="md:hidden text-[#111111] p-1"
          aria-label="Toggle navigation menu"
        >
          <Menu v-if="!mobileMenuOpen" class="w-5 h-5" />
          <X v-else class="w-5 h-5" />
        </button>
      </div>
    </div>

    <!-- Mobile Slide-over / Dropdown Menu -->
    <transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0 -translate-y-2"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-2"
    >
      <div
        v-if="mobileMenuOpen"
        class="md:hidden bg-white border-b border-[#E7E7E5] px-6 py-6 space-y-4"
        @click="mobileMenuOpen = false"
      >
        <div class="space-y-3 text-sm font-semibold uppercase tracking-wider">
          <!-- Mobile Search Input -->
          <div class="pb-3 border-b border-[#E7E7E5] flex items-center gap-2">
            <input
              v-model="searchQuery"
              @keydown.enter="handleSearch(); mobileMenuOpen = false;"
              type="text"
              placeholder="Search footwear..."
              class="h-9 flex-1 border border-[#E7E7E5] bg-[#F5F5F3] px-3 text-xs text-[#111111] focus:bg-white focus:outline-none"
            />
            <button
              type="button"
              @click="handleSearch(); mobileMenuOpen = false;"
              class="h-9 px-3 bg-[#111111] text-white text-xs font-bold"
            >
              Search
            </button>
          </div>

          <!-- Mobile Cart Button -->
          <button
            type="button"
            @click="cart.openCart(); mobileMenuOpen = false;"
            class="w-full flex items-center justify-between py-2.5 text-[#111111] border-b border-[#F5F5F3]"
          >
            <span class="flex items-center gap-2">
              <ShoppingBag class="w-4 h-4" />
              <span>Shopping Bag</span>
            </span>
            <span class="px-2 py-0.5 bg-black text-white text-[10px] font-bold rounded-full">
              {{ cart.totalItems }}
            </span>
          </button>

          <router-link to="/" class="block py-2 text-[#111111] border-b border-[#F5F5F3]">
            Home
          </router-link>
          <router-link to="/products" class="block py-2 text-[#111111] border-b border-[#F5F5F3]">
            The Collection (Shop)
          </router-link>
          <router-link to="/products?category=running-shoes" class="block py-2 text-[#707070] hover:text-black">
            Running Shoes
          </router-link>
          <router-link to="/products?category=casual-sneakers" class="block py-2 text-[#707070] hover:text-black">
            Casual Sneakers
          </router-link>
          <router-link to="/products?category=formal-shoes" class="block py-2 text-[#707070] hover:text-black">
            Formal Shoes
          </router-link>
          <a
            href="/api/documentation"
            target="_blank"
            rel="noopener noreferrer"
            class="py-2 text-[#707070] hover:text-black flex items-center justify-between border-t border-[#F5F5F3]"
          >
            <span>Swagger API Documentation</span>
            <ExternalLink class="w-3.5 h-3.5" />
          </a>

          <div v-if="auth.isAdmin" class="pt-2">
            <router-link
              to="/admin/products"
              class="block py-2.5 px-3 bg-[#F5F5F3] text-black border border-[#E7E7E5] font-bold"
            >
              ⚙️ Admin Product Management
            </router-link>
          </div>

          <div class="pt-4 border-t border-[#E7E7E5]">
            <div v-if="auth.isAuthenticated" class="flex items-center justify-between">
              <div>
                <p class="text-xs font-bold text-[#111111]">{{ auth.user?.name }}</p>
                <p class="text-[10px] text-[#707070] uppercase">{{ auth.user?.role }}</p>
              </div>
              <button
                @click="handleLogout"
                class="text-xs uppercase font-bold text-red-600"
              >
                Sign Out
              </button>
            </div>
            <router-link
              v-else
              to="/login"
              class="block w-full text-center py-3 bg-[#111111] text-white text-xs font-bold uppercase tracking-widest"
            >
              Sign In / Register
            </router-link>
          </div>
        </div>
      </div>
    </transition>
  </header>
</template>

<script setup lang="ts">
import { ref, nextTick } from 'vue';
import { useRouter } from 'vue-router';
import { useWindowScroll } from '@vueuse/core';
import { Search, ShoppingBag, User as UserIcon, Menu, X, ExternalLink } from 'lucide-vue-next';
import { useAuthStore } from '@/stores/auth';
import { useCartStore } from '@/stores/cart';
import { cn } from '@/lib/utils';
import { toast } from 'vue-sonner';

const router = useRouter();
const auth = useAuthStore();
const cart = useCartStore();
const { y } = useWindowScroll();

const mobileMenuOpen = ref(false);
const userMenuOpen = ref(false);
const searchOpen = ref(false);
const searchQuery = ref('');
const searchInputRef = ref<HTMLInputElement | null>(null);

function toggleSearch() {
  searchOpen.value = !searchOpen.value;
  if (searchOpen.value) {
    nextTick(() => {
      searchInputRef.value?.focus();
    });
  }
}

function onSearchButtonClick() {
  if (searchOpen.value && searchQuery.value.trim()) {
    handleSearch();
  } else {
    toggleSearch();
  }
}

function handleSearch() {
  const queryStr = searchQuery.value.trim();
  router.push({ path: '/products', query: queryStr ? { search: queryStr } : {} });
  searchOpen.value = false;
  searchQuery.value = '';
}

async function handleLogout() {
  await auth.logout();
  toast.success('Signed out successfully.');
  router.push('/');
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white text-[#111111]">
    <Navbar />

    <main class="flex-1">
      <router-view v-slot="{ Component }">
        <transition name="fade" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <Footer />

    <!-- Slide-over Shopping Cart Drawer -->
    <CartDrawer />

    <!-- Vue Sonner Toast Container -->
    <Toaster position="bottom-right" :closeButton="true" />
  </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { Toaster } from 'vue-sonner';
import Navbar from '@/components/layout/Navbar.vue';
import Footer from '@/components/layout/Footer.vue';
import CartDrawer from '@/components/cart/CartDrawer.vue';
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();

onMounted(async () => {
  await auth.initialize();
});
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>

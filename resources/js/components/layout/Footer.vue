<template>
  <footer class="bg-white border-t border-[#E7E7E5] mt-24 text-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid grid-cols-1 md:grid-cols-12 gap-10 lg:gap-16">
        <!-- Brand column -->
        <div class="md:col-span-5 space-y-4">
          <router-link to="/" class="inline-block">
            <span class="text-2xl font-black tracking-tighter text-[#111111] uppercase select-none">
              STEPUP
            </span>
          </router-link>
          <p class="text-[#707070] text-xs leading-relaxed max-w-sm">
            Every step tells a story. Contemporary footwear engineered for everyday movement, functional aesthetics, and mindful self-expression.
          </p>
          <div class="pt-2 text-[11px] text-[#999999] tracking-wider uppercase font-semibold">
            Catalog & Product Management Platform
          </div>
        </div>

        <!-- Collections Column -->
        <div class="md:col-span-3 space-y-3">
          <h3 class="text-[11px] font-bold uppercase tracking-widest text-[#111111]">
            Categories
          </h3>
          <ul class="space-y-2 text-[#707070]">
            <li>
              <router-link to="/products?category=running-shoes" class="hover:text-black transition-colors">
                Running Shoes
              </router-link>
            </li>
            <li>
              <router-link to="/products?category=casual-sneakers" class="hover:text-black transition-colors">
                Casual Sneakers
              </router-link>
            </li>
            <li>
              <router-link to="/products?category=formal-shoes" class="hover:text-black transition-colors">
                Formal Shoes
              </router-link>
            </li>
            <li>
              <router-link to="/products" class="hover:text-black transition-colors">
                All Footwear
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Quick Links -->
        <div class="md:col-span-2 space-y-3">
          <h3 class="text-[11px] font-bold uppercase tracking-widest text-[#111111]">
            Navigation
          </h3>
          <ul class="space-y-2 text-[#707070]">
            <li>
              <router-link to="/" class="hover:text-black transition-colors">Home</router-link>
            </li>
            <li>
              <router-link to="/products" class="hover:text-black transition-colors">The Collection</router-link>
            </li>
            <li>
              <router-link to="/login" class="hover:text-black transition-colors">Account Portal</router-link>
            </li>
            <li v-if="auth.isAdmin">
              <router-link to="/admin/products" class="hover:text-black transition-colors font-semibold text-black">
                Admin Panel
              </router-link>
            </li>
          </ul>
        </div>

        <!-- Brand Statement / Editorial -->
        <div class="md:col-span-2 space-y-3">
          <h3 class="text-[11px] font-bold uppercase tracking-widest text-[#111111]">
            Philosophy
          </h3>
          <p class="text-[#707070] text-[11px] leading-relaxed">
            Form follows motion. Designed with intention, refined with restraint.
          </p>
        </div>
      </div>

      <!-- Copyright bar -->
      <div class="mt-16 pt-8 border-t border-[#E7E7E5] flex flex-col sm:flex-row items-center justify-between text-[#707070] text-[11px] gap-4">
        <p>&copy; {{ new Date().getFullYear() }} STEPUP Footwear. All rights reserved.</p>
        <div class="flex items-center gap-6">
          <span>Laravel 11 REST API</span>
          <span>&bull;</span>
          <span>Vue 3 TypeScript</span>
          <span>&bull;</span>
          <span>Sanctum RBAC</span>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { useAuthStore } from '@/stores/auth';

const auth = useAuthStore();
</script>

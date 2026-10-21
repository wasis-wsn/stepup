<template>
  <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white border border-[#E7E7E5] p-8 sm:p-10 shadow-xs">
      <!-- Brand Header -->
      <div class="text-center space-y-2">
        <router-link to="/" class="inline-block">
          <span class="text-2xl font-black tracking-tighter text-[#111111] uppercase">
            STEPUP
          </span>
        </router-link>
        <h2 class="text-xl font-bold tracking-tight text-[#111111]">
          Sign in to your account
        </h2>
        <p class="text-xs text-[#707070]">
          Enter your registered email and credentials below.
        </p>
      </div>

      <!-- Quick Demo Login Presets for Reviewer Convenience -->
      <div class="p-3.5 bg-[#F5F5F3] border border-[#E7E7E5] text-xs space-y-2">
        <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-[#707070]">
          <span>Reviewer Demo Login</span>
          <span class="text-black">1-Click</span>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <button
            type="button"
            @click="quickLogin('admin@stepup.test', 'password123')"
            :disabled="loading"
            class="py-1.5 px-3 bg-[#111111] text-white hover:bg-neutral-800 text-[11px] font-semibold transition-colors flex items-center justify-center gap-1 cursor-pointer"
          >
            <span>👑</span>
            <span>Admin Demo</span>
          </button>
          <button
            type="button"
            @click="quickLogin('user@stepup.test', 'password123')"
            :disabled="loading"
            class="py-1.5 px-3 bg-white text-[#111111] border border-[#E7E7E5] hover:border-black text-[11px] font-semibold transition-colors flex items-center justify-center gap-1 cursor-pointer"
          >
            <span>👤</span>
            <span>User Demo</span>
          </button>
        </div>
      </div>

      <!-- Error Alert -->
      <div
        v-if="errorMessage"
        class="p-3 bg-red-50 border border-red-200 text-xs text-red-600 font-medium"
      >
        {{ errorMessage }}
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="space-y-5">
        <Input
          v-model="email"
          label="Email Address"
          type="email"
          placeholder="name@example.com"
          required
        />

        <div class="relative">
          <Input
            v-model="password"
            label="Password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="••••••••"
            required
          />
          <button
            type="button"
            @click="showPassword = !showPassword"
            class="absolute right-3 top-7 text-[11px] font-semibold uppercase tracking-wider text-[#707070] hover:text-black"
          >
            {{ showPassword ? 'Hide' : 'Show' }}
          </button>
        </div>

        <Button
          type="submit"
          class="w-full"
          size="lg"
          :loading="loading"
        >
          Sign In
        </Button>
      </form>

      <!-- Bottom link to Register -->
      <div class="text-center text-xs text-[#707070] pt-4 border-t border-[#F5F5F3]">
        Don't have an account yet?
        <router-link to="/register" class="font-bold text-black hover:underline ml-1">
          Create an account
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { toast } from 'vue-sonner';
import Input from '@/components/ui/Input.vue';
import Button from '@/components/ui/Button.vue';

const router = useRouter();
const route = useRoute();
const auth = useAuthStore();

const email = ref('');
const password = ref('');
const showPassword = ref(false);
const loading = ref(false);
const errorMessage = ref('');

async function quickLogin(e: string, p: string) {
  email.value = e;
  password.value = p;
  await handleSubmit();
}

async function handleSubmit() {
  if (!email.value || !password.value) {
    errorMessage.value = 'Please fill in both email and password.';
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  try {
    await auth.login({ email: email.value, password: password.value });
    toast.success(`Welcome back, ${auth.user?.name}!`);

    // Route redirect logic
    const redirect = (route.query.redirect as string) || (auth.isAdmin ? '/admin/products' : '/');
    router.push(redirect);
  } catch (err: any) {
    if (err.response?.data?.message) {
      errorMessage.value = err.response.data.message;
    } else {
      errorMessage.value = 'Invalid email or password.';
    }
    toast.error(errorMessage.value);
  } finally {
    loading.value = false;
  }
}
</script>

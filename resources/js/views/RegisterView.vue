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
          Create customer account
        </h2>
        <p class="text-xs text-[#707070]">
          Join the community for product updates and saved access.
        </p>
      </div>

      <!-- Error Alert -->
      <div
        v-if="errorMessage"
        class="p-3 bg-red-50 border border-red-200 text-xs text-red-600 font-medium"
      >
        {{ errorMessage }}
      </div>

      <!-- Form -->
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <Input
          v-model="name"
          label="Full Name"
          type="text"
          placeholder="Jane Doe"
          required
        />

        <Input
          v-model="email"
          label="Email Address"
          type="email"
          placeholder="jane@example.com"
          required
        />

        <Input
          v-model="password"
          label="Password (min 8 chars)"
          type="password"
          placeholder="••••••••"
          required
          minlength="8"
        />

        <Input
          v-model="passwordConfirmation"
          label="Confirm Password"
          type="password"
          placeholder="••••••••"
          required
          minlength="8"
        />

        <div class="pt-2">
          <Button
            type="submit"
            class="w-full"
            size="lg"
            :loading="loading"
          >
            Create Account
          </Button>
        </div>
      </form>

      <!-- Bottom link to Login -->
      <div class="text-center text-xs text-[#707070] pt-4 border-t border-[#F5F5F3]">
        Already have an account?
        <router-link to="/login" class="font-bold text-black hover:underline ml-1">
          Sign In
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth';
import { toast } from 'vue-sonner';
import Input from '@/components/ui/Input.vue';
import Button from '@/components/ui/Button.vue';

const router = useRouter();
const auth = useAuthStore();

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const loading = ref(false);
const errorMessage = ref('');

async function handleSubmit() {
  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = 'Passwords do not match.';
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  try {
    await auth.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });
    toast.success('Registration successful! Welcome to STEPUP.');
    router.push('/');
  } catch (err: any) {
    if (err.response?.data?.errors) {
      const errList = Object.values(err.response.data.errors).flat();
      errorMessage.value = errList.join(' ');
    } else if (err.response?.data?.message) {
      errorMessage.value = err.response.data.message;
    } else {
      errorMessage.value = 'Registration failed. Please check your inputs.';
    }
    toast.error(errorMessage.value);
  } finally {
    loading.value = false;
  }
}
</script>

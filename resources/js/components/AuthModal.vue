<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 sm:p-8 border border-gray-100 animate-scale-up">
      <!-- Close button -->
      <button
        @click="$emit('close')"
        class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-700 transition"
      >
        ✕
      </button>

      <!-- Tabs -->
      <div class="flex border-b border-gray-200 mb-6">
        <button
          @click="activeTab = 'login'"
          class="pb-3 px-4 font-semibold text-sm transition-all relative"
          :class="activeTab === 'login' ? 'text-black border-b-2 border-black' : 'text-gray-400 hover:text-gray-700'"
        >
          Sign In
        </button>
        <button
          @click="activeTab = 'register'"
          class="pb-3 px-4 font-semibold text-sm transition-all relative"
          :class="activeTab === 'register' ? 'text-black border-b-2 border-black' : 'text-gray-400 hover:text-gray-700'"
        >
          Create Account
        </button>
      </div>

      <!-- Quick Demo Login Presets -->
      <div v-if="activeTab === 'login'" class="mb-5 p-3.5 bg-gray-50 rounded-xl border border-gray-200/80">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Quick Demo Login</span>
          <span class="text-[10px] text-gray-400">1-click test</span>
        </div>
        <div class="grid grid-cols-2 gap-2">
          <button
            type="button"
            @click="quickLogin('admin@stepup.test', 'password123')"
            :disabled="loading"
            class="py-1.5 px-3 bg-black hover:bg-neutral-800 text-white rounded-lg text-xs font-semibold transition flex items-center justify-center gap-1.5 disabled:opacity-50"
          >
            <span>👑</span>
            <span>Admin</span>
          </button>
          <button
            type="button"
            @click="quickLogin('user@stepup.test', 'password123')"
            :disabled="loading"
            class="py-1.5 px-3 bg-white hover:bg-gray-100 text-gray-800 border border-gray-300 rounded-lg text-xs font-semibold transition flex items-center justify-center gap-1.5 disabled:opacity-50"
          >
            <span>👤</span>
            <span>User</span>
          </button>
        </div>
      </div>

      <!-- Error Alert -->
      <div v-if="errorMessage" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600">
        {{ errorMessage }}
      </div>

      <!-- Login Form -->
      <form v-if="activeTab === 'login'" @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Email Address</label>
          <input
            v-model="loginForm.email"
            type="email"
            required
            placeholder="admin@stepup.test"
            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm transition"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1.5">Password</label>
          <input
            v-model="loginForm.password"
            type="password"
            required
            placeholder="••••••••"
            class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm transition"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3 bg-black hover:bg-neutral-800 text-white font-semibold text-sm rounded-xl transition shadow-sm disabled:opacity-50 flex items-center justify-center"
        >
          <span v-if="loading">Signing in...</span>
          <span v-else>Sign In</span>
        </button>
      </form>

      <!-- Register Form -->
      <form v-else @submit.prevent="handleRegister" class="space-y-3.5">
        <div>
          <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Full Name</label>
          <input
            v-model="registerForm.name"
            type="text"
            required
            placeholder="Jane Doe"
            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm transition"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Email</label>
          <input
            v-model="registerForm.email"
            type="email"
            required
            placeholder="jane@example.com"
            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm transition"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Password</label>
          <input
            v-model="registerForm.password"
            type="password"
            required
            minlength="8"
            placeholder="Min 8 characters"
            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm transition"
          />
        </div>

        <div>
          <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider mb-1">Confirm Password</label>
          <input
            v-model="registerForm.password_confirmation"
            type="password"
            required
            minlength="8"
            placeholder="Repeat password"
            class="w-full px-3.5 py-2 rounded-xl border border-gray-200 focus:outline-none focus:border-black text-sm transition"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full mt-2 py-3 bg-black hover:bg-neutral-800 text-white font-semibold text-sm rounded-xl transition shadow-sm disabled:opacity-50 flex items-center justify-center"
        >
          <span v-if="loading">Creating account...</span>
          <span v-else>Register as Customer</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const emit = defineEmits(['close', 'auth-success']);

const activeTab = ref('login');
const loading = ref(false);
const errorMessage = ref('');

const loginForm = ref({
  email: '',
  password: ''
});

const registerForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

async function quickLogin(email, password) {
  loginForm.value.email = email;
  loginForm.value.password = password;
  await handleLogin();
}

async function handleLogin() {
  loading.value = true;
  errorMessage.value = '';

  try {
    const res = await axios.post('/api/login', loginForm.value);
    const { token, user } = res.data;
    localStorage.setItem('stepup_token', token);
    localStorage.setItem('stepup_user', JSON.stringify(user));
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    emit('auth-success', { user, token });
    emit('close');
  } catch (err) {
    if (err.response?.data?.message) {
      errorMessage.value = err.response.data.message;
    } else {
      errorMessage.value = 'Failed to sign in. Please verify your credentials.';
    }
  } finally {
    loading.value = false;
  }
}

async function handleRegister() {
  loading.value = true;
  errorMessage.value = '';

  try {
    const res = await axios.post('/api/register', registerForm.value);
    const { token, user } = res.data;
    localStorage.setItem('stepup_token', token);
    localStorage.setItem('stepup_user', JSON.stringify(user));
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    emit('auth-success', { user, token });
    emit('close');
  } catch (err) {
    if (err.response?.data?.errors) {
      const errList = Object.values(err.response.data.errors).flat();
      errorMessage.value = errList.join(' ');
    } else if (err.response?.data?.message) {
      errorMessage.value = err.response.data.message;
    } else {
      errorMessage.value = 'Registration failed. Please check your details.';
    }
  } finally {
    loading.value = false;
  }
}
</script>

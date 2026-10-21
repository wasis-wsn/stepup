import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import type { User, LoginPayload, RegisterPayload } from '@/types';
import authService from '@/services/authService';

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null);
  const token = ref<string | null>(localStorage.getItem('stepup_token'));
  const loading = ref<boolean>(false);

  const isAuthenticated = computed(() => !!token.value && !!user.value);
  const isAdmin = computed(() => user.value?.role === 'admin');

  async function initialize(): Promise<void> {
    const savedToken = localStorage.getItem('stepup_token');
    const savedUser = localStorage.getItem('stepup_user');

    if (savedToken) {
      token.value = savedToken;
      if (savedUser) {
        try {
          user.value = JSON.parse(savedUser);
        } catch {
          user.value = null;
        }
      }

      try {
        const freshUser = await authService.getCurrentUser();
        user.value = freshUser;
        localStorage.setItem('stepup_user', JSON.stringify(freshUser));
      } catch {
        // Token invalid
        user.value = null;
        token.value = null;
        localStorage.removeItem('stepup_token');
        localStorage.removeItem('stepup_user');
      }
    }
  }

  async function login(payload: LoginPayload): Promise<void> {
    loading.value = true;
    try {
      const res = await authService.login(payload);
      token.value = res.token;
      user.value = res.user;
      localStorage.setItem('stepup_token', res.token);
      localStorage.setItem('stepup_user', JSON.stringify(res.user));
    } finally {
      loading.value = false;
    }
  }

  async function register(payload: RegisterPayload): Promise<void> {
    loading.value = true;
    try {
      const res = await authService.register(payload);
      token.value = res.token;
      user.value = res.user;
      localStorage.setItem('stepup_token', res.token);
      localStorage.setItem('stepup_user', JSON.stringify(res.user));
    } finally {
      loading.value = false;
    }
  }

  async function logout(): Promise<void> {
    try {
      await authService.logout();
    } finally {
      token.value = null;
      user.value = null;
      localStorage.removeItem('stepup_token');
      localStorage.removeItem('stepup_user');
    }
  }

  return {
    user,
    token,
    loading,
    isAuthenticated,
    isAdmin,
    initialize,
    login,
    register,
    logout,
  };
});

import apiClient from '@/lib/api';
import type { LoginPayload, RegisterPayload, AuthResponse, User } from '@/types';

export const authService = {
  async login(payload: LoginPayload): Promise<AuthResponse> {
    const response = await apiClient.post<AuthResponse>('/login', payload);
    return response.data;
  },

  async register(payload: RegisterPayload): Promise<AuthResponse> {
    const response = await apiClient.post<AuthResponse>('/register', payload);
    return response.data;
  },

  async logout(): Promise<void> {
    try {
      await apiClient.post('/logout');
    } finally {
      localStorage.removeItem('stepup_token');
      localStorage.removeItem('stepup_user');
    }
  },

  async getCurrentUser(): Promise<User> {
    const response = await apiClient.get<{ user: User }>('/user');
    return response.data.user;
  },
};

export default authService;

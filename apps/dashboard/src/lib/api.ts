import axios, { AxiosError, InternalAxiosRequestConfig } from 'axios';
import { useAuthStore } from '@/stores/auth';

const AUTH_BASE = process.env.NEXT_PUBLIC_AUTH_API_URL || 'https://auth-service-api.mahamexpo.sa/api/v1';
const EXPO_BASE = process.env.NEXT_PUBLIC_EXPO_API_URL || 'https://expo-service-api.mahamexpo.sa/api/v1';

function createApiClient(baseURL: string) {
  const client = axios.create({ baseURL });

  client.interceptors.request.use((config: InternalAxiosRequestConfig) => {
    const token = useAuthStore.getState().token;
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    const locale = typeof window !== 'undefined' ? localStorage.getItem('locale') || 'ar' : 'ar';
    config.headers['Accept-Language'] = locale;
    return config;
  });

  client.interceptors.response.use(
    (response) => response,
    async (error: AxiosError) => {
      const originalRequest = error.config as InternalAxiosRequestConfig & { _retry?: boolean };
      const errorCode = (error.response?.data as Record<string, string>)?.error_code;
      
      if (
        error.response?.status === 401 &&
        !originalRequest._retry &&
        errorCode !== 'invalid_credentials'
      ) {
        originalRequest._retry = true;
        const currentToken = useAuthStore.getState().token;
        
        if (currentToken) {
          try {
            const refreshClient = axios.create({ baseURL: AUTH_BASE });
            const refreshRes = await refreshClient.post('/auth/refresh', {}, {
              headers: { Authorization: `Bearer ${currentToken}` }
            });
            const newToken = refreshRes.data.data.token;
            useAuthStore.getState().setToken(newToken);
            originalRequest.headers.Authorization = `Bearer ${newToken}`;
            return client(originalRequest);
          } catch {
            useAuthStore.getState().logout();
            if (typeof window !== 'undefined') {
              window.location.href = '/login';
            }
          }
        } else {
          useAuthStore.getState().logout();
          if (typeof window !== 'undefined') {
            window.location.href = '/login';
          }
        }
      }
      return Promise.reject(error);
    }
  );

  return client;
}

export const authApi = createApiClient(AUTH_BASE);
export const expoApi = createApiClient(EXPO_BASE);

export type ApiResponse<T = unknown> = {
  success: boolean;
  message?: string;
  data: T;
  error_code?: string;
  errors?: Record<string, string[]>;
  pagination?: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
  };
};

import { create } from 'zustand';
import { persist } from 'zustand/middleware';

export interface User {
  id: string;
  name: string;
  email: string;
  phone: string;
  avatar: string | null;
  status: string;
  email_verified_at: string | null;
  phone_verified_at: string | null;
  roles: string[];
  permissions: string[];
}

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  setAuth: (user: User, token: string) => void;
  setToken: (token: string) => void;
  setUser: (user: User) => void;
  logout: () => void;
  hasPermission: (permission: string) => boolean;
  hasRole: (role: string) => boolean;
  isSuperAdmin: () => boolean;
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      token: null,
      isAuthenticated: false,
      setAuth: (user, token) => set({ user, token, isAuthenticated: true }),
      setToken: (token) => set({ token }),
      setUser: (user) => set({ user }),
      logout: () => set({ user: null, token: null, isAuthenticated: false }),
      hasPermission: (permission) => {
        const { user } = get();
        if (!user) return false;
        if (user.roles.includes('super-admin')) return true;
        return user.permissions.includes(permission);
      },
      hasRole: (role) => {
        const { user } = get();
        return user?.roles.includes(role) || false;
      },
      isSuperAdmin: () => {
        const { user } = get();
        return user?.roles.includes('super-admin') || false;
      },
    }),
    { name: 'auth-storage' }
  )
);

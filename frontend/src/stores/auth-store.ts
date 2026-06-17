import { create } from 'zustand';

interface AuthState {
  isAuthenticated: boolean;
  setIsAuthenticated: (status: boolean) => void;
  openAuthModal: () => void;
  closeAuthModal: () => void;
  isAuthModalOpen: boolean;
}

export const useAuthStore = create<AuthState>((set) => ({
  isAuthenticated: typeof window !== 'undefined' ? !!localStorage.getItem('access_token') : false,
  setIsAuthenticated: (status) => set({ isAuthenticated: status }),
  isAuthModalOpen: false,
  openAuthModal: () => set({ isAuthModalOpen: true }),
  closeAuthModal: () => set({ isAuthModalOpen: false }),
}));

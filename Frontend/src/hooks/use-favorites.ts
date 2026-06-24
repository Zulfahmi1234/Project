import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import api from '@/lib/axios';
import { toast } from 'react-hot-toast';
import { useAuthStore } from '@/stores/auth-store';

export interface FavoriteLocation {
  id: number;
  city_name: string;
  latitude: number;
  longitude: number;
  country?: string;
  country_code?: string;
  timezone?: string;
}

export function useFavorites() {
  const isAuthenticated = useAuthStore(state => state.isAuthenticated);
  return useQuery({
    queryKey: ['favorites'],
    queryFn: async () => {
      if (typeof window === 'undefined') return [];
      const token = localStorage.getItem('access_token');
      if (!token) return [];
      
      const res = await api.get('/favorites');
      return res.data.data.favorites as FavoriteLocation[];
    },
    enabled: isAuthenticated,
  });
}


export function useAddFavorite() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: async (data: { city_name: string; latitude: number; longitude: number; country: string; country_code: string; timezone: string }) => {
      const res = await api.post('/favorites', data);
      return res.data;
    },
    onMutate: () => {
      const toastId = toast.loading('Menyimpan lokasi...');
      return { toastId };
    },
    onSuccess: (data, variables, context) => {
      queryClient.invalidateQueries({ queryKey: ['favorites'] });
      toast.success('Lokasi berhasil disimpan.', { id: context?.toastId });
    },
    onError: (err: any, variables, context) => {
      toast.error(err.response?.data?.message || 'Gagal menyimpan lokasi.', { id: context?.toastId });
    }
  });
}

export function useRemoveFavorite() {
  const queryClient = useQueryClient();
  return useMutation({
    mutationFn: async (id: number) => {
      const res = await api.delete(`/favorites/${id}`);
      return res.data;
    },
    onMutate: () => {
      const toastId = toast.loading('Menghapus lokasi...');
      return { toastId };
    },
    onSuccess: (data, variables, context) => {
      queryClient.invalidateQueries({ queryKey: ['favorites'] });
      toast.success('Lokasi dihapus dari favorit.', { id: context?.toastId });
    },
    onError: (err: any, variables, context) => {
      toast.error(err.response?.data?.message || 'Gagal menghapus lokasi.', { id: context?.toastId });
    }
  });
}

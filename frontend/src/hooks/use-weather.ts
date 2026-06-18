import { useQuery } from '@tanstack/react-query';
import api from '@/lib/axios';
import { useAuthStore } from '@/stores/auth-store';

export function useCurrentWeather(lat?: number, lng?: number, cityName?: string) {
  const isAuthenticated = useAuthStore(state => state.isAuthenticated);
  return useQuery({
    queryKey: ['weather', 'current', lat, lng, cityName],
    queryFn: async () => {
      if (!lat || !lng) return null;
      const res = await api.get('/weather/current', {
        params: { latitude: lat, longitude: lng, city_name: cityName }
      });
      return res.data.data;
    },
    enabled: !!lat && !!lng && isAuthenticated,
  });
}

export function useForecast(lat?: number, lng?: number, cityName?: string) {
  const isAuthenticated = useAuthStore(state => state.isAuthenticated);
  return useQuery({
    queryKey: ['weather', 'forecast', lat, lng, cityName],
    queryFn: async () => {
      if (!lat || !lng) return null;
      const res = await api.get('/weather/forecast', {
        params: { latitude: lat, longitude: lng, city_name: cityName }
      });
      return res.data.data;
    },
    enabled: !!lat && !!lng && isAuthenticated,
  });
}

export function useBoundary(cityName?: string) {
  const isAuthenticated = useAuthStore(state => state.isAuthenticated);
  return useQuery({
    queryKey: ['boundary', cityName],
    queryFn: async () => {
      if (!cityName) return null;
      const res = await api.get('/geocoding/boundary', {
        params: { q: cityName }
      });
      return res.data.data.boundary;
    },
    enabled: !!cityName && isAuthenticated,
    staleTime: 1000 * 60 * 60 * 24, // 24 hours caching boundary
  });
}

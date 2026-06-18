import { useQuery } from '@tanstack/react-query';
import axios from 'axios';

export interface GeocodingResult {
  id: number;
  name: string;
  latitude: number;
  longitude: number;
  country: string;
  admin1?: string;
  feature_code?: string;
}

export function useGeocoding(query: string) {
  return useQuery({
    queryKey: ['geocoding', query],
    queryFn: async () => {
      // Endpoint ini langsung dipanggil oleh FE sesuai dengan Agents.md agar debounce real-time lebih cepat
      if (!query || query.length < 2) return [];
      const res = await axios.get(`https://geocoding-api.open-meteo.com/v1/search`, {
        params: {
          name: query,
          count: 5,
          language: 'id',
          format: 'json'
        }
      });
      return (res.data.results || []) as GeocodingResult[];
    },
    enabled: query.length >= 2,
    staleTime: 1000 * 60 * 5, // 5 menit cache
  });
}

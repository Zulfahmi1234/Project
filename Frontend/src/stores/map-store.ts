import { create } from 'zustand';

export interface SelectedLocation {
  name: string;
  latitude: number;
  longitude: number;
  country: string;
  country_code: string;
  timezone: string;
}

interface MapState {
  viewState: {
    longitude: number;
    latitude: number;
    zoom: number;
  };
  selectedLocation: SelectedLocation | null;
  boundaryGeoJson: any | null;
  isFlying: boolean;
  setViewState: (viewState: Partial<MapState['viewState']>) => void;
  setSelectedLocation: (location: SelectedLocation | null) => void;
  setBoundaryGeoJson: (geoJson: any | null) => void;
  setIsFlying: (isFlying: boolean) => void;
}

export const useMapStore = create<MapState>((set) => ({
  viewState: {
    longitude: 113.9213,
    latitude: -0.7893,
    zoom: 4, // Default view (Indonesia)
  },
  selectedLocation: null,
  boundaryGeoJson: null,
  isFlying: false,
  setViewState: (newViewState) =>
    set((state) => ({ viewState: { ...state.viewState, ...newViewState } })),
  setSelectedLocation: (location) => set({ selectedLocation: location }),
  setBoundaryGeoJson: (geoJson) => set({ boundaryGeoJson: geoJson }),
  setIsFlying: (isFlying) => set({ isFlying }),
}));

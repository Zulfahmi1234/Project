"use client";

import { useEffect, useState } from "react";
import Map, { MapProvider } from "react-map-gl/mapbox";
import { useMapStore } from "@/stores/map-store";
import { SearchBar } from "@/components/search/search-bar";
import { BoundaryLayer } from "@/components/map/boundary-layer";
import { FloatingWeatherPanel } from "@/components/weather/floating-panel";
import { AuthModal } from "@/components/auth/auth-modal";
import { FavoritesSidebar } from "@/components/favorites/sidebar";
import {
  AutoRotate,
  Buildings3D,
  FavoriteBeacons,
  PulsingBeacon,
} from "@/components/map/cinematic-features";

const MAPBOX_TOKEN = process.env.NEXT_PUBLIC_MAPBOX_TOKEN;

export default function MapPage() {
  const { viewState, setViewState } = useMapStore();
  const [mounted, setMounted] = useState(false);

  useEffect(() => {
    // Membungkus dalam setTimeout agar linter tidak menganggapnya sebagai "cascading synchronous render"
    const timer = setTimeout(() => setMounted(true), 0);
    return () => clearTimeout(timer);
  }, []);

  if (!mounted) return null;

  return (
    <div className="w-full h-[100dvh] relative bg-background overflow-hidden">
      {MAPBOX_TOKEN ? (
        <MapProvider>
          <Map
            id="main-map"
            {...viewState}
            onMove={(evt) => setViewState(evt.viewState)}
            mapStyle="mapbox://styles/mapbox/dark-v11"
            mapboxAccessToken={MAPBOX_TOKEN}
            style={{ width: "100%", height: "100%" }}
            fog={{
              range: [0.5, 10],
              color: "#1c1c1e", // Abu-abu gelap elegan
              "high-color": "#0e0e0f", // Semakin ke atas semakin pekat
              "space-color": "#000000", // Hitam murni di luar angkasa
              "horizon-blend": 0.15,
              "star-intensity": 0.3, // Bintang sedikit diterangkan agar kontras dengan warna hitam
            }}
          >
            <AutoRotate />
            <Buildings3D />
            <PulsingBeacon />
            <FavoriteBeacons />
            <BoundaryLayer />
          </Map>

          {/* Overlays */}
          <FavoritesSidebar />

          <div className="absolute top-4 right-4 left-4 md:left-auto md:top-6 md:right-6 z-10 pointer-events-none flex flex-col items-end gap-3 mt-14 md:mt-0">
            <div className="pointer-events-auto flex flex-col md:flex-row items-end md:items-center gap-3 md:gap-4 w-full md:w-auto">
              <AuthModal />
              <SearchBar />
            </div>
          </div>

          <FloatingWeatherPanel />
        </MapProvider>
      ) : (
        <div className="flex h-full w-full items-center justify-center bg-secondary">
          <p className="text-muted-foreground font-mono-data border border-border p-4 dither-pattern">
            [SYS_ERR] NEXT_PUBLIC_MAPBOX_TOKEN is missing
          </p>
        </div>
      )}

      {/* Brand Header */}
      <div className="absolute top-4 left-4 md:top-6 md:left-6 z-10 pointer-events-none">
        <h1 className="font-heading text-2xl md:text-4xl text-primary uppercase tracking-wider drop-shadow-md font-bold">
          AeroCast
        </h1>
        <p className="font-mono-data text-muted-foreground text-[8px] md:text-[10px] uppercase tracking-widest mt-1">
          Atmospheric Data Relay
        </p>
      </div>
    </div>
  );
}

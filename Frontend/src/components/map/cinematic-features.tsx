"use client";

import { useEffect, useRef } from "react";
import { Layer, Marker, useMap } from "react-map-gl/mapbox";
import { useMapStore } from "@/stores/map-store";
import { useFavorites } from "@/hooks/use-favorites";

// 1. Cinematic Auto-Rotate
export function AutoRotate() {
  const { "main-map": map } = useMap();
  const isFlying = useMapStore(state => state.isFlying);
  const selectedLocation = useMapStore(state => state.selectedLocation);

  // Efek untuk reset kamera ke normal saat panel cuaca ditutup (selectedLocation jadi null)
  useEffect(() => {
    if (!map) return;
    if (!selectedLocation) {
      // Kembali normal (tampak atas, tidak miring)
      map.easeTo({
        pitch: 0,
        bearing: 0,
        duration: 1500,
        essential: true
      });
    }
  }, [selectedLocation, map]);

  useEffect(() => {
    if (!map) return;
    let animationId: number;
    let isInteracting = false;
    
    const setInteracting = () => { isInteracting = true; };
    const setNotInteracting = () => { isInteracting = false; };

    // Daftar semua event interaksi user (mouse, sentuhan, zoom, rotasi, tilt)
    const interactionEvents = [
      'mousedown', 'mouseup', 'dragstart', 'dragend',
      'touchstart', 'touchend', 'zoomstart', 'zoomend',
      'rotatestart', 'rotateend', 'pitchstart', 'pitchend'
    ];

    interactionEvents.forEach(event => {
      if (event.endsWith('start') || event === 'mousedown') {
        map.on(event, setInteracting);
      } else {
        map.on(event, setNotInteracting);
      }
    });

    const rotate = () => {
      // Hanya berjalan jika ada kota yang dipilih (panel cuaca terbuka),
      // map tidak diinteraksi, dan tidak sedang flyTo
      if (!isInteracting && !isFlying && selectedLocation) {
        const currentZoom = map.getZoom();
        
        if (currentZoom >= 10) {
          // --- MODE DRONE (ZOOM IN) ---
          map.setBearing(map.getBearing() + 0.015);
          
          const currentPitch = map.getPitch();
          if (currentPitch < 60) {
            map.setPitch(Math.min(60, currentPitch + 0.3));
          }
        } else {
          // --- KEMBALI NORMAL (ZOOM OUT TERLALU JAUH) ---
          const currentPitch = map.getPitch();
          const currentBearing = map.getBearing();
          
          // Turunkan pitch perlahan ke 0
          if (currentPitch > 0) {
            map.setPitch(Math.max(0, currentPitch - 0.5));
          }
          
          // Putar bearing perlahan kembali ke Utara (0) menggunakan pelemahan eksponensial
          if (Math.abs(currentBearing) > 0.1) {
            map.setBearing(currentBearing * 0.95);
          } else if (currentBearing !== 0) {
            map.setBearing(0);
          }
        }
      }
      animationId = requestAnimationFrame(rotate);
    };
    
    rotate();
    return () => {
      cancelAnimationFrame(animationId);
      interactionEvents.forEach(event => {
        if (event.endsWith('start') || event === 'mousedown') {
          map.off(event, setInteracting);
        } else {
          map.off(event, setNotInteracting);
        }
      });
    };
  }, [map, isFlying, selectedLocation]);

  return null;
}

// 2. 3D Buildings Layer
export function Buildings3D() {
  return (
    <Layer 
      id="3d-buildings"
      source="composite"
      source-layer="building"
      filter={["==", "extrude", "true"]}
      type="fill-extrusion"
      minzoom={14}
      paint={{
        "fill-extrusion-color": "#1e2235", // Warna gelap untuk menyatu dengan dark mode
        "fill-extrusion-height": ["get", "height"],
        "fill-extrusion-base": ["get", "min_height"],
        "fill-extrusion-opacity": 0.7
      }}
    />
  );
}

// 3. Pulsing Location Beacon
export function PulsingBeacon() {
  const selectedLocation = useMapStore(state => state.selectedLocation);

  if (!selectedLocation) return null;

  return (
    <Marker 
      latitude={selectedLocation.latitude} 
      longitude={selectedLocation.longitude} 
      anchor="center"
      style={{ pointerEvents: 'none' }}
    >
      <div className="relative flex items-center justify-center w-12 h-12">
        {/* Gelombang luar yang memancar */}
        <div className="absolute w-full h-full rounded-full bg-primary/40 animate-ping" style={{ animationDuration: '2s' }}></div>
        {/* Titik pusat solid */}
        <div className="absolute w-3 h-3 rounded-full bg-primary shadow-[0_0_15px_theme('colors.primary.DEFAULT')]"></div>
      </div>
    </Marker>
  );
}

// 4. Favorite Location Beacons
export function FavoriteBeacons() {
  const { data: favorites } = useFavorites();
  const selectedLocation = useMapStore(state => state.selectedLocation);
  const setSelectedLocation = useMapStore(state => state.setSelectedLocation);

  if (!favorites || favorites.length === 0) return null;

  return (
    <>
      {favorites.map((fav) => {
        // Jangan render jika kota tersebut sedang dipilih (karena sudah dirender oleh PulsingBeacon utama)
        if (selectedLocation && fav.city_name === selectedLocation.name) return null;

        return (
          <Marker 
            key={`fav-${fav.id}`}
            latitude={fav.latitude} 
            longitude={fav.longitude} 
            anchor="center"
          >
            <div 
              className="relative flex items-center justify-center w-8 h-8 cursor-pointer group"
              onClick={() => setSelectedLocation({
                name: fav.city_name,
                latitude: fav.latitude,
                longitude: fav.longitude,
                country: fav.country,
                country_code: fav.country_code,
                timezone: fav.timezone,
              })}
              title={`Buka ${fav.city_name}`}
            >
              {/* Gelombang luar berwarna cyan/biru untuk favorit */}
              <div className="absolute w-full h-full rounded-full bg-cyan-500/30 animate-ping" style={{ animationDuration: '3s' }}></div>
              <div className="absolute w-2 h-2 rounded-full bg-cyan-400 shadow-[0_0_10px_theme('colors.cyan.400')] group-hover:scale-150 transition-transform"></div>
            </div>
          </Marker>
        );
      })}
    </>
  );
}

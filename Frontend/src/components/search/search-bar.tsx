"use client";

import { useState, useEffect, useRef } from "react";
import { useGeocoding, GeocodingResult } from "@/hooks/use-geocoding";
import { Search, MapPin, Loader2 } from "lucide-react";
import { useMapStore } from "@/stores/map-store";
import { useMap } from "react-map-gl/mapbox";
import { motion, AnimatePresence } from "framer-motion";

export function SearchBar() {
  const [inputValue, setInputValue] = useState("");
  const [debouncedValue, setDebouncedValue] = useState("");
  const [isFocused, setIsFocused] = useState(false);
  const [isSelecting, setIsSelecting] = useState(false); // Guard: cegah klik ganda saat flyTo
  const containerRef = useRef<HTMLDivElement>(null);
  
  const { data: results, isLoading } = useGeocoding(debouncedValue);
  const { setSelectedLocation, setIsFlying } = useMapStore();
  const { "main-map": map } = useMap(); // Gunakan ID map

  const flyToTimeoutRef = useRef<NodeJS.Timeout | null>(null);

  // Debounce manual
  useEffect(() => {
    const timer = setTimeout(() => {
      setDebouncedValue(inputValue);
    }, 300);
    return () => clearTimeout(timer);
  }, [inputValue]);

  // Click outside listener
  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
        setIsFocused(false);
      }
    }
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  const handleSelectCity = (city: GeocodingResult) => {
    if (isSelecting) return; // Guard: cegah klik ganda
    setIsSelecting(true);
    setInputValue(city.name);
    setIsFocused(false);
    
    // Penentuan level zoom dinamis (Negara vs Kota)
    let targetZoom = 11; // Default zoom untuk kota
    if (city.feature_code?.startsWith('PCL')) {
      targetZoom = 4; // Zoom negara
    } else if (city.feature_code?.startsWith('ADM1')) {
      targetZoom = 6; // Zoom provinsi
    } else if (city.feature_code?.startsWith('ADM2')) {
      targetZoom = 8; // Zoom kabupaten
    }

    if (flyToTimeoutRef.current) clearTimeout(flyToTimeoutRef.current);

    // Fitur Cinematic Zoom Mapbox
    if (map) {
      setIsFlying(true); // Mulai transisi awan
      map.flyTo({
        center: [city.longitude, city.latitude],
        zoom: targetZoom,
        pitch: 0, // Paksa tampak atas saat terbang
        bearing: 0,
        duration: 3500, // Diperlambat untuk efek sinematik
        curve: 1.5, // Membuat efek terbang lebih melengkung (zoom out lalu zoom in)
        essential: true,
      });

      // Tunda fetching data sampai animasi terbang (1.8 detik) selesai
      flyToTimeoutRef.current = setTimeout(() => {
        setIsFlying(false); // Selesaikan transisi awan
        setSelectedLocation({
          name: city.name,
          latitude: city.latitude,
          longitude: city.longitude,
          country: city.country || "Unknown",
          country_code: city.country_code || "XX",
          timezone: city.timezone || "UTC",
        });
        setIsSelecting(false); // Buka kembali akses setelah animasi selesai
      }, 3500);
    } else {
      setSelectedLocation({
        name: city.name,
        latitude: city.latitude,
        longitude: city.longitude,
      });
      setIsSelecting(false);
    }
  };

  return (
    <div ref={containerRef} className="relative w-full md:w-96 max-w-[calc(100vw-3rem)]">
      <div className="relative group">
        <div className="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-muted-foreground">
          {isLoading && isFocused ? (
            <Loader2 className="w-5 h-5 animate-spin text-primary" />
          ) : (
            <Search className="w-5 h-5" />
          )}
        </div>
        <input
          type="text"
          className="w-full glass-panel text-foreground font-mono-data px-10 py-3 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition-all hard-shadow placeholder:text-muted-foreground/70"
          placeholder="ENTER LOCATION // CITY"
          value={inputValue}
          onChange={(e) => setInputValue(e.target.value)}
          onFocus={() => setIsFocused(true)}
        />
      </div>

      <AnimatePresence>
        {isFocused && inputValue.length >= 2 && (
          <motion.div
            initial={{ opacity: 0, y: -5 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -5 }}
            transition={{ duration: 0.15 }}
            className="absolute top-full left-0 right-0 mt-3 glass-panel z-50 overflow-hidden hard-shadow"
          >
            {results && results.length > 0 ? (
              <ul className="flex flex-col">
                {results.map((city, index) => (
                  <li key={city.id}>
                    <button
                      onClick={() => handleSelectCity(city)}
                      disabled={isSelecting}
                      className="w-full flex items-start gap-3 p-3 hover:bg-white/10 transition-colors text-left disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                      <MapPin className="w-4 h-4 text-primary shrink-0 mt-0.5" />
                      <div>
                        <p className="font-heading text-sm text-foreground uppercase tracking-wide font-bold">
                          {city.name}
                        </p>
                        <p className="font-mono-data text-[10px] text-muted-foreground uppercase tracking-widest mt-1">
                          {city.admin1 ? `${city.admin1}, ` : ""}{city.country}
                        </p>
                      </div>
                    </button>
                    {index < results.length - 1 && (
                      <div className="h-px w-full dither-pattern" />
                    )}
                  </li>
                ))}
              </ul>
            ) : !isLoading ? (
              <div className="p-4 text-center">
                <p className="font-mono-data text-xs text-muted-foreground uppercase tracking-wider">
                  [SYS_ERR] Location not found
                </p>
              </div>
            ) : null}
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}

"use client";

import { useState, useEffect } from "react";
import { useFavorites, useRemoveFavorite } from "@/hooks/use-favorites";
import { useMapStore } from "@/stores/map-store";
import { useMap } from "react-map-gl/mapbox";
import { MapPin, Trash2, ChevronRight, ChevronLeft, Lock } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { useAuthStore } from "@/stores/auth-store";

export function FavoritesSidebar() {
  const [isOpen, setIsOpen] = useState(false);
  const { isAuthenticated, openAuthModal } = useAuthStore();
  const { data: favorites, isLoading } = useFavorites();
  const { mutate: removeFavorite, isPending: isRemoving } = useRemoveFavorite();
  const { setSelectedLocation, setIsFlying } = useMapStore();
  const { "main-map": map } = useMap();

  useEffect(() => {
    if (!map) return;
    
    const handleMapMove = () => {
      if (isOpen) {
        setIsOpen(false);
      }
    };

    map.on('dragstart', handleMapMove);
    
    return () => {
      map.off('dragstart', handleMapMove);
    };
  }, [map, isOpen]);

  const handleSelect = (fav: any) => {
    setIsOpen(false); // Tutup sidebar saat dipilih
    if (map) {
      setIsFlying(true);
      map.flyTo({
        center: [fav.longitude, fav.latitude],
        zoom: 11, // Standard city zoom
        pitch: 0, // Terbang dari sudut pandang atas kepala
        bearing: 0,
        duration: 3500,
        curve: 1.5,
        essential: true,
      });

      setTimeout(() => {
        setIsFlying(false);
        setSelectedLocation({
          name: fav.city_name,
          latitude: fav.latitude,
          longitude: fav.longitude,
          country: fav.country,
          country_code: fav.country_code,
          timezone: fav.timezone,
        });
      }, 3500);
    } else {
      setSelectedLocation({
        name: fav.city_name,
        latitude: fav.latitude,
        longitude: fav.longitude,
        country: fav.country,
        country_code: fav.country_code,
        timezone: fav.timezone,
      });
    }
  };

  return (
    <>
      {/* Toggle Button */}
      <button
        onClick={() => setIsOpen(!isOpen)}
        className="absolute top-1/2 -translate-y-1/2 left-0 z-40 glass-panel hard-shadow p-2 text-primary hover:bg-white/10 transition-colors rounded-r-md border-l-0"
        title="Toggle Saved Locations"
      >
        {isOpen ? <ChevronLeft className="w-6 h-6" /> : <ChevronRight className="w-6 h-6" />}
      </button>

      {/* Sidebar Panel */}
      <AnimatePresence>
        {isOpen && (
          <motion.div
            initial={{ x: "-100%" }}
            animate={{ x: 0 }}
            exit={{ x: "-100%" }}
            transition={{ type: "spring", stiffness: 400, damping: 22, mass: 0.8 }}
            className="absolute top-0 left-0 bottom-0 w-80 glass-panel border-r-2 border-border hard-shadow z-30 flex flex-col pt-24 pb-6 px-4"
          >
            <h2 className="font-heading text-lg text-primary uppercase tracking-wider mb-4 dither-pattern pb-2 border-b border-border">
              Saved Coordinates
            </h2>

            <div className="flex-1 overflow-y-auto pr-2 space-y-3 custom-scrollbar">
              {!isAuthenticated ? (
                <div className="flex flex-col items-center justify-center gap-4 text-center mt-10">
                  <Lock className="w-8 h-8 text-muted-foreground mx-auto" />
                  <p className="font-mono-data text-xs text-muted-foreground uppercase">
                    [ AUTHENTICATION_REQUIRED ]<br/>
                    Login required to view bookmarks
                  </p>
                  <button
                    onClick={openAuthModal}
                    className="bg-primary text-primary-foreground font-heading uppercase px-4 py-2 text-xs hard-shadow hover:bg-primary/90 transition-colors mx-auto mt-2"
                  >
                    System Login
                  </button>
                </div>
              ) : isLoading ? (
                <p className="font-mono-data text-xs text-muted-foreground animate-pulse">
                  [ SCANNING_DATABANKS... ]
                </p>
              ) : favorites && favorites.length > 0 ? (
                favorites.map((fav) => (
                  <div 
                    key={fav.id} 
                    className="group bg-black/40 border border-border p-2 hover:border-primary transition-colors flex items-center justify-between"
                  >
                    <button 
                      onClick={() => handleSelect(fav)}
                      className="flex-1 text-left flex items-center gap-3 overflow-hidden"
                    >
                      <MapPin className="w-4 h-4 text-primary shrink-0" />
                      <span className="font-heading text-sm uppercase text-foreground truncate">
                        {fav.city_name}
                      </span>
                    </button>
                    <button
                      onClick={() => removeFavorite(fav.id)}
                      disabled={isRemoving}
                      className="text-muted-foreground hover:text-destructive p-2 shrink-0 transition-colors"
                      title="Remove Bookmark"
                    >
                      <Trash2 className="w-4 h-4" />
                    </button>
                  </div>
                ))
              ) : (
                <p className="font-mono-data text-xs text-muted-foreground opacity-70 mt-4">
                  [ NO_SAVED_LOCATIONS ]
                </p>
              )}
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </>
  );
}

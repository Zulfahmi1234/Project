"use client";

import { useState } from "react";
import { useMapStore } from "@/stores/map-store";
import { useCurrentWeather, useForecast } from "@/hooks/use-weather";
import { useAddFavorite, useFavorites, useRemoveFavorite } from "@/hooks/use-favorites";
import { motion, AnimatePresence } from "framer-motion";
import { Droplets, Wind, Thermometer, X, Bookmark, Loader2, Lock } from "lucide-react";
import dayjs from "dayjs";
import 'dayjs/locale/id';
import { LineChart, Line, XAxis, YAxis, Tooltip, ResponsiveContainer } from "recharts";
import { useAuthStore } from "@/stores/auth-store";

dayjs.locale('id');

export function FloatingWeatherPanel() {
  const { isAuthenticated, openAuthModal } = useAuthStore();
  const selectedLocation = useMapStore(state => state.selectedLocation);
  const setSelectedLocation = useMapStore(state => state.setSelectedLocation);
  const { data: favorites } = useFavorites();
  const { mutate: removeFavorite, isPending: isRemovingFav } = useRemoveFavorite();
  
  const favoritedItem = favorites?.find(fav => fav.city_name === selectedLocation?.name);
  const isFavorited = !!favoritedItem;
  const { data: current, isLoading: loadingCurrent } = useCurrentWeather(
    selectedLocation?.latitude, 
    selectedLocation?.longitude, 
    selectedLocation?.name
  );
  
  const { data: forecast, isLoading: loadingForecast } = useForecast(
    selectedLocation?.latitude, 
    selectedLocation?.longitude, 
    selectedLocation?.name
  );

  const { mutate: addFavorite, isPending: isAddingFav } = useAddFavorite();

  const isVisible = !!selectedLocation;

  return (
    <AnimatePresence>
      {isVisible && (
        <motion.div
          key={selectedLocation.name}
          initial={{ opacity: 0, y: 40, scale: 0.95 }}
          animate={{ opacity: 1, y: 0, scale: 1 }}
          exit={{ opacity: 0, y: 40, scale: 0.95 }}
          transition={{ type: "spring", stiffness: 400, damping: 22, mass: 0.8 }}
          className="absolute bottom-4 md:bottom-6 left-1/2 -translate-x-1/2 z-20 w-[calc(100vw-2rem)] md:w-[800px] glass-panel hard-shadow flex flex-col pointer-events-auto"
        >
            {/* Header */}
          <div className="flex justify-between items-center p-4 border-b border-border dither-pattern">
            <div>
              <h2 className="font-heading text-lg font-bold uppercase tracking-wider text-primary truncate max-w-[200px]">
                {selectedLocation.name}
              </h2>
              <p className="font-mono-data text-[10px] text-muted-foreground uppercase mt-1">
                {dayjs().format("DD MMM YYYY // HH:mm")}
              </p>
            </div>
            <div className="flex items-center gap-3">
              {isAuthenticated && (
                <button
                  onClick={() => {
                    if (isFavorited && favoritedItem) {
                      removeFavorite(favoritedItem.id);
                    } else {
                      addFavorite({
                        city_name: selectedLocation.name,
                        latitude: selectedLocation.latitude,
                        longitude: selectedLocation.longitude,
                        country: selectedLocation.country,
                        country_code: selectedLocation.country_code,
                        timezone: selectedLocation.timezone
                      });
                    }
                  }}
                  disabled={isAddingFav || isRemovingFav}
                  className={`transition-colors ${isFavorited ? 'text-primary' : 'text-muted-foreground hover:text-primary'}`}
                  title={isFavorited ? "Hapus dari Favorit" : "Simpan ke Favorit"}
                >
                  {(isAddingFav || isRemovingFav) ? (
                    <Loader2 className="w-5 h-5 animate-spin" />
                  ) : (
                    <Bookmark className="w-5 h-5" fill={isFavorited ? "currentColor" : "none"} />
                  )}
                </button>
              )}
              <button 
                onClick={() => setSelectedLocation(null)}
                className="text-muted-foreground hover:text-destructive transition-colors"
                title="Tutup Panel"
              >
                <X className="w-5 h-5" />
              </button>
            </div>
          </div>

          {/* Current Weather & Forecast or Login Prompt */}
          {!isAuthenticated ? (
            <div className="p-8 border-b border-border bg-black/20 flex flex-col items-center justify-center gap-4 text-center">
              <Lock className="w-8 h-8 text-muted-foreground" />
              <p className="font-mono-data text-xs text-muted-foreground uppercase">
                [ AUTHENTICATION_REQUIRED ]<br/>
                Login required to access atmospheric data
              </p>
              <button
                onClick={openAuthModal}
                className="bg-primary text-primary-foreground font-heading uppercase px-4 py-2 text-xs hard-shadow hover:bg-primary/90 transition-colors mt-2"
              >
                System Login
              </button>
            </div>
          ) : (
            <div className="flex flex-col md:flex-row w-full">
              {/* Current Weather */}
              <div className="p-4 border-b md:border-b-0 md:border-r border-border bg-black/20 w-full md:w-[35%] flex flex-col justify-center">
                {loadingCurrent ? (
                  <div className="h-24 flex items-center justify-center font-mono-data text-xs text-muted-foreground animate-pulse">
                    [ FETCHING_ATMOSPHERIC_DATA ]
                  </div>
                ) : current ? (
                  <div className="flex items-center justify-between">
                    <div>
                      <div className="text-5xl font-heading font-bold text-foreground">
                        {current.current.temperature}°
                      </div>
                      <div className="font-mono-data text-xs text-primary uppercase mt-2">
                        {current.current.condition}
                      </div>
                    </div>
                    <div className="space-y-3">
                      <div className="flex items-center gap-2 font-mono-data text-xs text-muted-foreground">
                        <Droplets className="w-4 h-4 text-primary shrink-0" />
                        HUMIDITY: {current.current.humidity}%
                      </div>
                      <div className="flex items-center gap-2 font-mono-data text-xs text-muted-foreground">
                        <Wind className="w-4 h-4 text-primary shrink-0" />
                        WIND: {current.current.wind_speed} km/h
                      </div>
                      <div className="flex items-center gap-2 font-mono-data text-xs text-muted-foreground">
                        <Thermometer className="w-4 h-4 text-primary shrink-0" />
                        FEELS: {current.current.feels_like}°
                      </div>
                    </div>
                  </div>
                ) : (
                  <div className="h-24 flex items-center justify-center font-mono-data text-xs text-destructive">
                    [ DATA_UNAVAILABLE ]
                  </div>
                )}
              </div>

              {/* Forecast */}
              <div className="p-4 bg-black/40 w-full md:w-[65%] flex flex-col justify-center">
                <h3 className="font-mono-data text-xs text-muted-foreground uppercase mb-4 tracking-widest">
                  7-DAY_PROJECTION
                </h3>
                {loadingForecast ? (
                  <div className="h-28 flex items-center justify-center font-mono-data text-xs text-muted-foreground animate-pulse">
                    [ SIMULATING_FORECAST ]
                  </div>
                ) : forecast ? (
                  <div className="h-28 w-full min-w-0">
                    <ResponsiveContainer width="99%" height={112}>
                      <LineChart data={forecast.forecast} margin={{ top: 10, right: 10, bottom: 10, left: -10 }}>
                        <XAxis 
                          dataKey="day_name" 
                          stroke="var(--color-muted-foreground)" 
                          fontSize={11} 
                          tickLine={false} 
                          axisLine={false} 
                          fontFamily="var(--font-mono-data)"
                          tickMargin={12}
                        />
                        <YAxis 
                          stroke="var(--color-muted-foreground)" 
                          fontSize={11} 
                          tickLine={false} 
                          axisLine={false}
                          tickFormatter={(val) => `${val}°`}
                          fontFamily="var(--font-mono-data)"
                          domain={[(dataMin: number) => Math.floor(dataMin) - 2, (dataMax: number) => Math.ceil(dataMax) + 2]}
                          tickMargin={12}
                        />
                        <Tooltip 
                          contentStyle={{ backgroundColor: 'var(--color-card)', borderColor: 'var(--color-border)', borderRadius: 0, fontFamily: 'var(--font-mono-data)', fontSize: '10px' }}
                          labelStyle={{ color: 'var(--color-foreground)', fontWeight: 'bold' }}
                          formatter={(value: any) => [`${value}°`, 'Suhu']}
                        />
                        <Line 
                          isAnimationActive={false}
                          connectNulls={true}
                          dataKey="temperature_max" 
                          stroke="var(--color-primary)" 
                          strokeWidth={2} 
                          dot={{ fill: 'var(--color-primary)', r: 3, strokeWidth: 0 }} 
                          activeDot={{ r: 5, fill: 'var(--color-primary)', stroke: 'none' }} 
                        />
                      </LineChart>
                    </ResponsiveContainer>
                  </div>
                ) : (
                   <div className="h-28 flex items-center justify-center font-mono-data text-xs text-destructive">
                    [ PROJECTION_FAILED ]
                  </div>
                )}
              </div>
            </div>
          )}
        </motion.div>
      )}
    </AnimatePresence>
  );
}

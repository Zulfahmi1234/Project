"use client";

import { useEffect, useState, useMemo } from "react";
import { Source, Layer } from "react-map-gl/mapbox";
import { useMapStore } from "@/stores/map-store";
import { useBoundary } from "@/hooks/use-weather";

// Helper mengubah Polygon menjadi MultiLineString agar mendukung line-trim-offset
function convertToLineStringFeature(feature: any) {
  if (!feature || !feature.geometry) return feature;
  
  const geom = feature.geometry;
  let newCoordinates = [];
  
  if (geom.type === "Polygon") {
    newCoordinates = geom.coordinates;
  } else if (geom.type === "MultiPolygon") {
    geom.coordinates.forEach((poly: any) => {
      newCoordinates.push(...poly);
    });
  } else {
    return feature; // Biarkan jika sudah bentuk line
  }

  return {
    ...feature,
    geometry: {
      type: "MultiLineString",
      coordinates: newCoordinates,
    }
  };
}

export function BoundaryLayer() {
  const selectedLocation = useMapStore(state => state.selectedLocation);
  const setBoundaryGeoJson = useMapStore(state => state.setBoundaryGeoJson);
  const [trimOffset, setTrimOffset] = useState<[number, number]>([0, 1]);
  const [fillOpacity, setFillOpacity] = useState(0);
  
  const { data: boundaryData } = useBoundary(selectedLocation?.name);

  // Konversi GeoJSON ke format MultiLineString
  const processedData = useMemo(() => {
    if (!boundaryData) return null;
    // Jika responnya feature collection, ubah fitur pertamanya
    if (boundaryData.type === "FeatureCollection") {
      return {
        ...boundaryData,
        features: boundaryData.features.map(convertToLineStringFeature)
      };
    }
    return convertToLineStringFeature(boundaryData);
  }, [boundaryData]);

  useEffect(() => {
    if (processedData) {
      setBoundaryGeoJson(processedData);
      setTrimOffset([0, 1]); // Mulai dari 0 (kosong)
      setFillOpacity(0);
      
      let start: number;
      let animationFrame: number;
      const duration = 1000; // Durasi garis diputar dipercepat (1 detik)

      // Fungsi ease-in cubic (mulai lambat, lalu mempercepat)
      const easeIn = (t: number) => t * t * t;

      const animate = (timestamp: number) => {
        if (!start) start = timestamp;
        const progress = (timestamp - start) / duration;
        
        if (progress < 1) {
          // Terapkan efek ease-in pada progress
          const easedProgress = easeIn(progress);
          // Animasi menggambar garis
          setTrimOffset([0, 1 - easedProgress]);
          animationFrame = requestAnimationFrame(animate);
        } else {
          // Selesai menggambar
          setTrimOffset([0, 0]);
          setFillOpacity(0.08); // Munculkan warnanya
        }
      };
      
      animationFrame = requestAnimationFrame(animate);
      return () => cancelAnimationFrame(animationFrame);
    } else {
      setBoundaryGeoJson(null);
    }
  }, [processedData, setBoundaryGeoJson]);

  if (!processedData) return null;

  return (
    <>
      {/* Source & Layer untuk Fill warna (menggunakan data Polygon asli agar tidak rusak) */}
      <Source id="boundary-source-fill" type="geojson" data={boundaryData}>
        <Layer
          id="boundary-fill"
          type="fill"
          paint={{
            "fill-color": "#38BDF8",
            "fill-opacity": fillOpacity,
            "fill-opacity-transition": { duration: 800, delay: 0 }
          }}
        />
      </Source>

      {/* Source & Layer untuk Garis animasi (menggunakan MultiLineString hasil konversi) */}
      <Source id="boundary-source-line" type="geojson" data={processedData} lineMetrics={true}>
        <Layer
          id="boundary-line"
          type="line"
          paint={{
            "line-color": "#38BDF8",
            "line-width": 3,
            "line-trim-offset": trimOffset,
          }}
          layout={{
            "line-cap": "round",
            "line-join": "round"
          }}
        />
      </Source>
    </>
  );
}

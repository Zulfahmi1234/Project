"use client";

import { useMemo } from "react";
import { useMapStore } from "@/stores/map-store";
import { motion, AnimatePresence } from "framer-motion";

export function CloudTransition() {
  const isFlying = useMapStore(state => state.isFlying);

  // Gunakan useMemo agar fungsi acak (Math.random) tidak dipanggil berkali-kali saat render
  const clouds = useMemo(() => {
    return Array.from({ length: 15 }).map((_, i) => ({
      id: i,
      randomX: `${(Math.random() - 0.5) * 150}vw`,
      randomY: `${(Math.random() - 0.5) * 150}vh`,
      randomRot: Math.random() * 360,
      randomCloudNum: Math.floor(Math.random() * 7) + 1,
      startZ: -3000 - (Math.random() * 2000),
      duration: 2.2 + Math.random() * 0.8,
      delay: Math.random() * 0.8,
      endRotOffset: Math.random() * 60 - 30
    }));
  }, []);

  return (
    <AnimatePresence>
      {isFlying && (
        <div 
          className="pointer-events-none fixed inset-0 z-[100] flex items-center justify-center overflow-hidden"
          style={{ perspective: "800px", perspectiveOrigin: "center center" }}
        >
          {clouds.map((cloud) => (
            <motion.div
              key={cloud.id}
              initial={{ 
                opacity: 0, 
                x: cloud.randomX, 
                y: cloud.randomY, 
                z: cloud.startZ,
                rotate: cloud.randomRot 
              }}
              animate={{ 
                opacity: [0, 1, 1, 0], 
                z: 1000, 
                rotate: cloud.randomRot + cloud.endRotOffset 
              }}
              exit={{ opacity: 0 }}
              transition={{ 
                duration: cloud.duration,
                ease: "easeIn", 
                delay: cloud.delay
              }}
              className="absolute w-[500px] h-[500px] md:w-[800px] md:h-[800px] opacity-90"
              style={{
                backgroundImage: `url('/Cloud (${cloud.randomCloudNum}).png')`,
                backgroundSize: "contain",
                backgroundRepeat: "no-repeat",
                backgroundPosition: "center"
              }}
            />
          ))}
          
          {/* Kabut putih/gelap yang menyelimuti layar saat kita "menembus" bagian tertebal dari kumpulan awan */}
          <motion.div 
            initial={{ opacity: 0 }}
            animate={{ opacity: [0, 0.6, 0] }}
            transition={{ duration: 1.5, delay: 0.8, ease: "easeInOut" }}
            className="absolute inset-0 bg-white/50 mix-blend-overlay"
          />
        </div>
      )}
    </AnimatePresence>
  );
}

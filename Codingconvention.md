# Coding Convention — AeroCast

> Dokumen ini wajib diikuti oleh semua anggota.
> Attach bersama AGENTS.md saat sesi AI agar kode yang digenerate langsung konsisten.

---

## Frontend (Next.js / TypeScript)

### File & Folder

| Konteks                   | Convention                        | Contoh                 |
| ------------------------- | --------------------------------- | ---------------------- |
| Folder komponen           | `kebab-case`                      | `components/weather/`  |
| File komponen React       | `kebab-case.tsx`                  | `floating-panel.tsx`   |
| File custom hook          | `kebab-case.ts`, awalan `use-`    | `use-weather.ts`       |
| File utilitas / lib       | `kebab-case.ts`                   | `axios.ts`, `utils.ts` |
| File tipe TypeScript      | `kebab-case.ts`                   | `weather.ts`           |
| File store (Zustand)      | `kebab-case.ts`, akhiran `-store` | `map-store.ts`         |
| File halaman (App Router) | selalu `page.tsx`                 | `app/map/page.tsx`     |
| File layout               | selalu `layout.tsx`               | `app/layout.tsx`       |

### Komponen React

```tsx
// ✅ Nama komponen: PascalCase
export default function FloatingPanel() { ... }
export function MetricBadge() { ... }

// ✅ Props interface: PascalCase + suffix "Props"
interface FloatingPanelProps {
  cityName: string
  isVisible: boolean
  onClose: () => void
}

// ❌ Hindari
export default function floating_panel() { ... }   // snake_case
export default function floatingPanel() { ... }    // camelCase
```

### Variabel & Fungsi

```ts
// ✅ Variabel & fungsi biasa: camelCase
const cityName = "Banda Aceh"
const weatherData = await fetchWeather()
function formatTemperature(temp: number) { ... }

// ✅ Konstanta global / konfigurasi: SCREAMING_SNAKE_CASE
const MAX_FAVORITES = 10
const DEFAULT_ZOOM = 11
const MAPBOX_STYLE_URL = "mapbox://styles/mapbox/dark-v11"

// ✅ Boolean: awalan is / has / should
const isLoading = true
const hasError = false
const shouldShowPanel = true
```

### Custom Hooks

```ts
// ✅ Selalu awalan "use", kembalikan objek bernama (bukan array)
export function useWeather(lat: number, lng: number) {
  const { data, isLoading, isError } = useQuery({ ... })
  return { weatherData: data, isLoading, isError }
}

// ❌ Hindari mengembalikan array seperti useState (kecuali benar-benar perlu)
return [data, isLoading]  // susah dibaca saat dipakai
```

### TypeScript Types

```ts
// ✅ Type & Interface: PascalCase
interface WeatherData {
  city: string;
  temperature: number;
  humidity: number;
}

type ForecastDay = {
  date: string;
  tempMax: number;
  tempMin: number;
};

// ✅ Enum: PascalCase untuk nama, SCREAMING_SNAKE_CASE untuk nilai
enum WeatherCondition {
  SUNNY = "SUNNY",
  CLOUDY = "CLOUDY",
  RAINY = "RAINY",
}
```

### Import Order

```ts
// Urutan import yang konsisten:
// 1. Library eksternal
import { useState, useEffect } from "react";
import { motion } from "framer-motion";
import dayjs from "dayjs";

// 2. Komponen internal
import FloatingPanel from "@/components/weather/floating-panel";
import MetricBadge from "@/components/weather/metric-badge";

// 3. Hooks
import { useWeather } from "@/hooks/use-weather";

// 4. Tipe
import type { WeatherData } from "@/types/weather";

// 5. Utilitas / lib
import { cn } from "@/lib/utils";
```

### Tailwind CSS

```tsx
// ✅ Gunakan fungsi cn() dari lib/utils untuk class kondisional
import { cn } from "@/lib/utils"

<div className={cn(
  "rounded-2xl border p-6",
  isVisible ? "opacity-100" : "opacity-0",
  isDark && "bg-black/30"
)} />

// ❌ Hindari string concatenation langsung
<div className={"rounded-2xl " + (isVisible ? "opacity-100" : "opacity-0")} />
```

---

## Backend (Laravel / PHP)

### File & Folder

| Konteks      | Convention                               | Contoh                                |
| ------------ | ---------------------------------------- | ------------------------------------- |
| Controller   | `PascalCase` + suffix `Controller`       | `WeatherController.php`               |
| Service      | `PascalCase` + suffix `Service`          | `OpenMeteoService.php`                |
| Model        | `PascalCase`, singular                   | `FavoriteLocation.php`                |
| Migration    | `snake_case`, format `verb_noun_table`   | `create_favorite_locations_table.php` |
| Form Request | `PascalCase`, format `ActionNounRequest` | `StoreFavoriteRequest.php`            |
| Route file   | `snake_case`                             | `api.php`                             |

### Class & Method

```php
// ✅ Nama class: PascalCase
class WeatherController extends Controller { }
class OpenMeteoService { }

// ✅ Nama method: camelCase
public function getCurrentWeather(Request $request) { }
public function fetchFromApi(float $lat, float $lng): array { }

// ✅ Method Controller mengikuti konvensi REST Laravel:
// index, show, store, update, destroy
// Tambahan boleh: current, forecast, search
public function index() { }    // GET /favorites
public function store() { }    // POST /favorites
public function destroy() { }  // DELETE /favorites/{id}
```

### Variabel & Properti

```php
// ✅ Variabel lokal & properti: camelCase
$weatherData = $this->openMeteoService->fetch($lat, $lng);
$cacheKey = "weather_forecast_{$lat}_{$lng}";
$favoriteLocation = FavoriteLocation::find($id);

// ✅ Konstanta class: SCREAMING_SNAKE_CASE
const CACHE_TTL_FORECAST = 3600;      // 1 jam dalam detik
const CACHE_TTL_BOUNDARY = 86400;     // 24 jam dalam detik
const NOMINATIM_RATE_LIMIT = 1;       // request per detik
```

### Database & Model

```php
// ✅ Nama tabel: snake_case, plural
// users, favorite_locations

// ✅ Nama kolom: snake_case
// city_name, country_code, created_at

// ✅ Nama relasi di Model: camelCase
public function favoriteLocations() { }   // hasMany
public function user() { }               // belongsTo

// ✅ Fillable: array of snake_case strings
protected $fillable = [
    'city_name',
    'latitude',
    'longitude',
    'country_code',
    'timezone',
];
```

### Response Format

```php
// ✅ Selalu gunakan format standar yang sudah disepakati
// Sukses:
return response()->json([
    'status'  => 'success',
    'message' => 'Data berhasil diambil.',
    'data'    => $data,
], 200);

// Error:
return response()->json([
    'status'  => 'error',
    'message' => 'Deskripsi error.',
], 422);

// ❌ Hindari return data mentah tanpa wrapper
return response()->json($data);
```

### Cache Key

```php
// ✅ Format cache key yang konsisten
"weather_current_{$lat}_{$lng}"       // cuaca terkini
"weather_forecast_{$lat}_{$lng}"      // forecast 7 hari
"boundary_{$queryNormalized}"         // GeoJSON boundary (lowercase, spasi → _)
```

---

## Git

### Branch

```
main              ← branch utama, selalu stable
dev               ← branch integrasi bersama
feat/nama-fitur   ← branch fitur baru
fix/nama-bug      ← branch perbaikan bug
```

Contoh:

```
feat/interactive-map
feat/floating-panel
fix/boundary-cache
```

### Commit Message

Format: `type: deskripsi singkat dalam bahasa Inggris`

| Type       | Kapan dipakai                               |
| ---------- | ------------------------------------------- |
| `feat`     | Menambah fitur baru                         |
| `fix`      | Memperbaiki bug                             |
| `docs`     | Perubahan dokumentasi                       |
| `style`    | Perubahan formatting, tidak mengubah logika |
| `refactor` | Refactor kode tanpa mengubah behavior       |
| `chore`    | Setup, config, dependency                   |

Contoh commit yang baik:

```
feat: add flyTo animation when city is selected
feat: render GeoJSON boundary layer on map
fix: floating panel not closing on map click
docs: update AGENTS.md with folder structure
chore: install mapbox-gl and react-map-gl
```

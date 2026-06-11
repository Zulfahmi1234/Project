# AGENTS.md — AeroCast Project Brief

> File ini wajib di-attach di setiap sesi baru dengan AI.
> Baca seluruh file ini sebelum memberikan bantuan apapun.

---

## Identitas Proyek

- **Nama Aplikasi:** AeroCast — Weather & Environment Dashboard
- **Kelompok:** OpenSky
- **Repo:** https://github.com/Zulfahmi1234/Project
- **Tujuan:** Website dashboard cuaca berbasis public API (Open-Meteo) untuk tugas kelompok.

---

## Anggota & Tanggung Jawab

| Nama                      | NIM       | Role               | Tanggung Jawab                         |
| ------------------------- | --------- | ------------------ | -------------------------------------- |
| Ikhlassul Amal            | 230705105 | Frontend Developer | UI, komponen, routing, auth client     |
| Muhammad Naufal Tiftazani | 230705117 | Backend Developer  | Laravel API, proxy Open-Meteo, JWT, DB |
| Zulfahmi                  | 230705116 | DevOps Engineer    | GitHub Actions CI/CD, deploy ke Render |

---

## Tech Stack

### Frontend (`/frontend`)

- **Framework:** Next.js (React) + TypeScript
- **Styling:** Tailwind CSS v4
- **Komponen:** shadcn/ui + radix-ui + headlessui
- **Auth:** Supabase Auth (client-side session management)
- **Data Fetching:** React Query (TanStack Query) + `axios` (instance terpusat di `lib/axios.ts` dengan interceptor Bearer token otomatis)
- **Peta Interaktif:** `mapbox-gl` + `react-map-gl` (peta 2D sebagai UI utama)
- **Animasi:** framer-motion, auto-animate, tailwindcss-animate
- **Chart:** recharts
- **Utility:** dayjs, react-hot-toast, next-themes, lucide-react

### Backend (`/backend`)

- **Framework:** Laravel 13 (PHP)
- **Auth:** Laravel Sanctum (token-based, bukan JWT murni)
- **HTTP Client:** Laravel HTTP Client (Guzzle wrapper — bukan Axios)
- **Cache:** Redis atau Laravel file cache (TTL: 1 jam untuk forecast)
- **Arsitektur:** Service Container, Service Provider, Facades

### Database

- **Engine:** PostgreSQL via Supabase
- **Tabel utama:** `users`, `favorite_locations`

### DevOps

- **CI/CD:** GitHub Actions
- **Hosting:** Render (FE + BE sebagai layanan terpisah)

---

## Arsitektur Sistem

```
[Browser / Next.js FE — Peta Mapbox GL JS]
        |
        | REST API (Bearer Token)
        v
[Laravel Backend — AeroCast API Gateway]
        |           |              |
        | DB         | HTTP Proxy   | HTTP Proxy
        v           v              v
  [PostgreSQL] [Open-Meteo API] [Nominatim API]
  via Supabase  (cuaca & forecast) (GeoJSON boundary)
```

**Aturan penting:**

- FE **tidak boleh** memanggil Open-Meteo atau Nominatim langsung. Semua wajib lewat BE (kecuali Geocoding search — lihat di bawah).
- FE memanggil Open-Meteo Geocoding API **langsung** hanya untuk fitur search kota (Fitur 4) karena bersifat real-time debounce.
- Data GeoJSON boundary dari Nominatim wajib lewat BE (untuk caching 24 jam dan menghindari rate limit).
- Semua response BE menggunakan format seragam: `{ "status": "success"|"error", "message": "...", "data": {...} }`

---

## Struktur Folder

Lihat `04-struktur-folder.md` untuk struktur lengkap. Ringkasan lokasi file penting:

```
frontend/src/
├── app/map/page.tsx          ← halaman utama peta
├── components/map/           ← komponen MapboxGL
├── components/weather/       ← floating panel & cuaca
├── components/search/        ← search bar & dropdown
├── components/favorites/     ← sidebar favorit
├── hooks/                    ← semua custom React hooks
├── lib/axios.ts              ← instance axios + interceptor token ⭐
├── stores/map-store.ts       ← state global peta
└── types/                    ← TypeScript types

backend/app/
├── Http/Controllers/Api/V1/  ← semua controller
├── Services/                 ← semua logika bisnis ⭐
└── Http/Requests/            ← validasi input
```

---

Base URL Backend: `https://[nama-app].onrender.com/api/v1`

| #   | Method | Endpoint                                            | Auth | Deskripsi                                                  |
| --- | ------ | --------------------------------------------------- | ---- | ---------------------------------------------------------- |
| 1   | POST   | `/auth/register`                                    | ❌   | Registrasi akun baru                                       |
| 2   | POST   | `/auth/login`                                       | ❌   | Login, dapat Bearer token                                  |
| 3   | POST   | `/auth/logout`                                      | ✅   | Invalidasi token                                           |
| 4   | GET    | `/weather/current?latitude=&longitude=&city_name=`  | ✅   | Data cuaca terkini                                         |
| 5   | GET    | `/weather/forecast?latitude=&longitude=&city_name=` | ✅   | Prakiraan 7 hari                                           |
| 6   | GET    | `/geocoding/search?q=&count=5`                      | ✅   | Cari kota (proxy Open-Meteo Geocoding)                     |
| 7   | GET    | `/favorites`                                        | ✅   | Daftar lokasi favorit user                                 |
| 8   | POST   | `/favorites`                                        | ✅   | Tambah lokasi favorit                                      |
| 9   | DELETE | `/favorites/{id}`                                   | ✅   | Hapus lokasi favorit                                       |
| 10  | GET    | `/geocoding/boundary?q=`                            | ✅   | GeoJSON batas wilayah kota (proxy Nominatim, cache 24 jam) |

**Token:** Disimpan di FE (localStorage atau cookie httpOnly), dikirim via header `Authorization: Bearer <token>`.

---

## Format Response Standar BE

```json
// Sukses
{ "status": "success", "message": "...", "data": { ... } }

// Error
{ "status": "error", "message": "...", "errors": { ... } }
```

---

## Fitur & Status

| Fitur                                    | PIC                          | Status         |
| ---------------------------------------- | ---------------------------- | -------------- |
| Auth (Register/Login/Logout)             | Naufal (BE)                  | ⬜ Belum mulai |
| Peta Interaktif 2D (Mapbox)              | Ikhlassul (FE)               | ⬜ Belum mulai |
| Dashboard Cuaca Terkini (Floating Panel) | Ikhlassul (FE)               | ⬜ Belum mulai |
| Prakiraan 7 Hari                         | Naufal (BE) + Ikhlassul (FE) | ⬜ Belum mulai |
| Pencarian Kota + Cinematic Zoom          | Ikhlassul (FE)               | ⬜ Belum mulai |
| Highlight Wilayah (GeoJSON Polygon)      | Naufal (BE) + Ikhlassul (FE) | ⬜ Belum mulai |
| Manajemen Lokasi Favorit                 | Naufal (BE) + Ikhlassul (FE) | ⬜ Belum mulai |
| CI/CD Pipeline                           | Zulfahmi (DevOps)            | ⬜ Belum mulai |
| Deploy ke Render                         | Zulfahmi (DevOps)            | ⬜ Belum mulai |

> **Update status ini setiap kali ada fitur yang selesai.**
> Gunakan: ⬜ Belum mulai / 🔄 In progress / ✅ Selesai

---

## Konvensi Kode

> Lihat `Codingconvention.md` untuk aturan lengkap. Ringkasan wajib di bawah.

### Frontend

| Konteks           | Convention                     | Contoh                       |
| ----------------- | ------------------------------ | ---------------------------- |
| File komponen     | `kebab-case.tsx`               | `floating-panel.tsx`         |
| Nama komponen     | `PascalCase`                   | `FloatingPanel`              |
| Variabel / fungsi | `camelCase`                    | `cityName`, `fetchWeather()` |
| Konstanta global  | `SCREAMING_SNAKE_CASE`         | `DEFAULT_ZOOM`               |
| Custom hook       | `kebab-case.ts`, awalan `use-` | `use-weather.ts`             |
| Boolean           | awalan `is` / `has` / `should` | `isLoading`, `hasError`      |

- Semua request HTTP ke BE wajib lewat instance `axios` dari `lib/axios.ts` — **jangan `fetch` native**
- Semua API call wajib lewat React Query (`useQuery` / `useMutation`)
- Class kondisional Tailwind wajib pakai `cn()` dari `lib/utils.ts`
- Error handling wajib pakai `react-hot-toast`

### Backend

| Konteks          | Convention                  | Contoh                            |
| ---------------- | --------------------------- | --------------------------------- |
| Controller       | `PascalCase` + `Controller` | `WeatherController.php`           |
| Service          | `PascalCase` + `Service`    | `OpenMeteoService.php`            |
| Model            | `PascalCase`, singular      | `FavoriteLocation.php`            |
| Method           | `camelCase`                 | `getCurrentWeather()`             |
| Variabel lokal   | `camelCase`                 | `$weatherData`                    |
| Tabel DB / kolom | `snake_case`                | `favorite_locations`, `city_name` |

- Semua logika bisnis masuk ke `Services/`, bukan di `Controllers/`
- Semua validasi input masuk ke `Requests/`, bukan di `Controllers/`
- Cache key: `weather_forecast_{lat}_{lng}`, `boundary_{query_normalized}`

### Git Commit

Format: `type: deskripsi` — contoh: `feat: add flyTo animation on city select`
Type: `feat` `fix` `docs` `style` `refactor` `chore`

---

## Prompt Template untuk Memulai Sesi Baru

### Untuk Ikhlassul (Frontend):

```
Saya Ikhlassul, Frontend Developer di proyek AeroCast.
Baca AGENTS.md ini terlebih dahulu sebagai konteks proyek kami.
[paste AGENTS.md]
Sekarang bantu saya: [deskripsikan task spesifik]
File yang relevan: [attach file kode yang sedang dikerjakan]
```

### Untuk Naufal (Backend):

```
Saya Naufal, Backend Developer di proyek AeroCast.
Baca AGENTS.md ini terlebih dahulu sebagai konteks proyek kami.
[paste AGENTS.md]
Sekarang bantu saya: [deskripsikan task spesifik]
File yang relevan: [attach file kode yang sedang dikerjakan]
```

### Untuk Zulfahmi (DevOps):

```
Saya Zulfahmi, DevOps Engineer di proyek AeroCast.
Baca AGENTS.md ini terlebih dahulu sebagai konteks proyek kami.
[paste AGENTS.md]
Sekarang bantu saya: [deskripsikan task spesifik]
Stack: Next.js (FE) di Render, Laravel 13 (BE) di Render, GitHub Actions untuk CI/CD.
```

---

## Catatan Penting

- Open-Meteo API tidak memerlukan API key. Base URL: `https://api.open-meteo.com/v1/`
- Open-Meteo Geocoding: `https://geocoding-api.open-meteo.com/v1/search`
- Nominatim (GeoJSON boundary): `https://nominatim.openstreetmap.org/search` — **rate limit 1 req/detik**, wajib cache di BE.
- Mapbox tile style gelap: gunakan style `mapbox://styles/mapbox/dark-v11` (memerlukan Mapbox Access Token)
- **Mapbox Access Token** wajib didaftarkan di [mapbox.com](https://account.mapbox.com). Simpan sebagai `NEXT_PUBLIC_MAPBOX_TOKEN` di `.env.local` FE dan di Render Environment Variables. Free tier cukup untuk kebutuhan tugas (50.000 map loads/bulan).
- Supabase digunakan **hanya** untuk PostgreSQL database dan auth client di FE. BE berkomunikasi langsung ke PostgreSQL via Laravel DB connection.
- Jangan pernah commit `.env` ke repo. Semua secret masuk ke GitHub Secrets / Render Environment Variables.
- Cache key Nominatim: `boundary_{q_normalized}` (TTL 24 jam). Cache key forecast: `weather_forecast_{latitude}_{longitude}` (TTL 1 jam).

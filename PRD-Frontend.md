# Product Requirements Document (PRD) - Frontend AeroCast

## 1. Ikhtisar Proyek (Project Overview)

**Nama Aplikasi:** AeroCast - Weather & Environment Dashboard  
**Kelompok:** OpenSky  
**Repositori:** [Zulfahmi1234/Project](https://github.com/Zulfahmi1234/Project)  
**Tujuan:** Mengembangkan antarmuka pengguna berbasis SPA (Single Page Application) di mana UI utamanya berupa peta dunia 2D interaktif. Aplikasi ini berfungsi sebagai dashboard cuaca dinamis berbasis data dari Open-Meteo dan OpenStreetMap, dirancang untuk tugas kelompok dengan arsitektur _client-server_ yang terpisah.

## 2. Tim Pengembang (Stakeholders)

| Nama                | Role               | Tanggung Jawab (Frontend)                                           |
| :------------------ | :----------------- | :------------------------------------------------------------------ |
| **Ikhlassul Amal**  | Frontend Developer | UI, komponen, routing, auth client, integrasi Mapbox, dan logic FE. |
| **Muhammad Naufal** | Backend Developer  | API endpoint (Laravel), proxy Open-Meteo & Nominatim.               |
| **Zulfahmi**        | DevOps Engineer    | CI/CD (GitHub Actions), Deployment ke Render.                       |

## 3. Tech Stack & Arsitektur Frontend

- **Framework Utama:** Next.js (React) + TypeScript (App Router: `app/map/page.tsx`).
- **Styling & UI:** Tailwind CSS v4, `shadcn/ui`, `radix-ui`, `headlessui`.
- **Manajemen State & Data Fetching:** React Query (TanStack Query) dan `zustand` (`stores/map-store.ts`).
- **HTTP Client:** `axios` (Instance terpusat dengan interceptor untuk _Bearer token_).
- **Peta Interaktif:** `mapbox-gl` dan `react-map-gl`.
- **Animasi:** `framer-motion`, `auto-animate`, `tailwindcss-animate`.
- **Visualisasi Data:** `recharts` (untuk grafik cuaca).
- **Utility & Tools:** `dayjs` (formatting waktu), `react-hot-toast` (notifikasi), `next-themes` (Dark/Light mode), `lucide-react` (Icons).
- **Auth (Client):** Supabase Auth (untuk session management di frontend).

**Aturan Arsitektur:**
Frontend **TIDAK BOLEH** memanggil API Open-Meteo atau Nominatim secara langsung, _kecuali_ untuk API Geocoding (pencarian kota) yang dipanggil secara _real-time/debounce_ langsung ke Open-Meteo. Data lainnya harus diroute melalui Backend Laravel.

## 4. Panduan Desain (Design System)

Mengadaptasi gaya desain **Atmospheric Brutalism** (Low-fi / High-tech) - memadukan estetika teknis/digital instrumen penerbangan dengan warna alam (_Deep Sepia_).

- **Palet Warna:** Warna latar gelap (`#141312`, `#26211C`), warna _Primary_ untuk pola struktural (`#967E5E`), _Tertiary_ untuk highlight (`#D9C5A0`). Menggunakan _dithering patterns_ atau tekstur _noise_ (3-5% opacity) dibandingkan gradient transisi halus.
- **Tipografi:**
  - **Space Grotesk:** Untuk _Headline_ (Uppercase, tight letter-spacing) dan data/angka teknis (_mono-data_).
  - **Inter:** Untuk _Body text_ dan deskripsi panjang.
- **Layout & Komponen:** Grid 12-kolom untuk desktop. Elemen interaktif menggunakan offset _hard shadow_ padat (tanpa _blur_, misalnya _offset_ 4px) dengan tepian sudut sedikit tumpul (0.25rem _soft radius_) agar terkesan seperti cetakan mesin industri (_stamped_).
- **Pemisah (Dividers):** Menggunakan pola _dithering_ 1px atau garis tegas (_solid_).

## 5. Konvensi Kode (Coding Conventions)

- **Penamaan File:** `kebab-case` (contoh: `floating-panel.tsx`, `use-weather.ts`).
- **Komponen React:** `PascalCase` untuk nama komponen dan interfaces props.
- **Fungsi & Variabel:** `camelCase`. Awalan `is`/`has`/`should` untuk boolean. Awalan `use-` untuk custom hooks.
- **Konstanta Global:** `SCREAMING_SNAKE_CASE` (contoh: `DEFAULT_ZOOM`).
- **Tailwind:** Semua kondisional styling harus menggunakan fungsi `cn()` dari `lib/utils.ts`.

## 6. Persyaratan Fitur (Feature Requirements)

### Fitur 1: Autentikasi dan Manajemen Pengguna

- **UI/UX:** Form Login dan Register yang aman.
- **Teknis:** Menerima JWT dari Backend dan menyimpannya (localStorage/cookie). Menampilkan _toast feedback_ dengan `react-hot-toast`.

### Fitur 2: Dashboard Cuaca Terkini (_Floating Panel_)

- **UI/UX:** _Floating Panel_ dengan gaya _glassmorphism_ di atas antarmuka peta yang menampilkan metrik cuaca langsung (suhu, angin, kelembapan).
- **Teknis:** Menggunakan `framer-motion` untuk efek _fade_ dan _slide-up_ saat komponen muncul, di-trigger ketika wilayah disorot/dipilih.

### Fitur 3: Prakiraan Cuaca 7 Hari

- **UI/UX:** Terintegrasi di dalam _Floating Panel_. Menampilkan grafik suhu menggunakan `recharts` dan kartu prakiraan harian.
- **Teknis:** Animasi masuk _slide_ pada muatan awal dan `auto-animate` saat melakukan rendering ulang data baru.

### Fitur 4: Pencarian Kota dengan _Cinematic Zoom_

- **UI/UX:** _Search bar_ dengan hasil _dropdown_.
- **Teknis:** Melakukan pemanggilan _Geocoding API_ dengan `react-query` (dibarengi mekanisme _debounce_ 300ms). Saat kota diklik, gunakan fungsi `flyTo()` dari Mapbox untuk transisi _zoom_ sinematik, dilanjutkan proses _fetching_ batas _GeoJSON_.

### Fitur 5: Manajemen Lokasi Favorit (_Bookmark_)

- **UI/UX:** Sidebar atau _dropdown_ yang berisi senarai tempat favorit dengan opsi simpan atau hapus (icon `lucide-react`).
- **Teknis:** Menyediakan notifikasi status aksi dengan _hot-toast_. Saat entri favorit diklik, peta mengeksekusi `flyTo()` layaknya fitur pencarian.

### Fitur 6: Peta Dunia 2D Interaktif

- **UI/UX:** Berperan sebagai UI latar layar penuhh (_full viewport_) dengan _dark-mode map styling_. Responsif minimal 60 fps _pan/zoom_.
- **Teknis:** Engine menggunakan `react-map-gl`. Gaya peta utama menggunakan referensi `mapbox://styles/mapbox/dark-v11`.

### Fitur 7: Sorotan Wilayah (_Highlight GeoJSON_)

- **UI/UX:** Saat batas wilayah didapatkan dari pencarian atau list favorit, render batas administratif kota dengan presisi.
- **Teknis:** Menambahkan Mapbox `source` dan `layer` jenis `line` (berwarna cyan/brand `#38BDF8` 2px) serta `fill` dengan opasitas rendah (0.08) untuk menunjukkan teritori yang dipilih.

## 7. Integrasi API

Semua akses data mengarah ke Backend (`https://[nama-app].onrender.com/api/v1`) menyertakan _header_ `Authorization: Bearer <token>`, kecuali Endpoint _Auth_.

| Fitur                | Method   | Endpoint Backend      | Parameter Utilitas                             |
| :------------------- | :------- | :-------------------- | :--------------------------------------------- |
| **Auth Register**    | `POST`   | `/auth/register`      | Body: name, email, password                    |
| **Auth Login**       | `POST`   | `/auth/login`         | Body: email, password                          |
| **Auth Logout**      | `POST`   | `/auth/logout`        | Memerlukan Token.                              |
| **Current Weather**  | `GET`    | `/weather/current`    | Query: `latitude`, `longitude`, `city_name`    |
| **7-Day Forecast**   | `GET`    | `/weather/forecast`   | Query: `latitude`, `longitude`, `city_name`    |
| **Favorit (List)**   | `GET`    | `/favorites`          | Menampilkan seluruh Bookmark user.             |
| **Favorit (Add)**    | `POST`   | `/favorites`          | Body: city, lat, lng, timezone, dll.           |
| **Favorit (Del)**    | `DELETE` | `/favorites/{id}`     | Menghapus bookmark dari ID.                    |
| **GeoJSON Bounding** | `GET`    | `/geocoding/boundary` | Query: `q` (nama area), merender batas Polygon |

_(Catatan khusus: Khusus untuk Geocoding saat user mengetik query di search bar, FE memanggil langsung ke endpoint Open-Meteo `https://geocoding-api.open-meteo.com/v1/search?name={query}` untuk menghindari latensi proxy backend)._

## 8. Langkah Berikutnya

- Inisialisasi repositori Next.js dengan ekstensi _Tailwind CSS v4_.
- Membangun fondasi `lib/axios.ts` untuk intersepsi Token.
- Setup UI MapboxGL sederhana di layar utama (`app/map/page.tsx`).
- Implementasi _Atmospheric Brutalism Design System_ pada `tailwind.config.ts` (atau equivalent v4) dan globals.css.

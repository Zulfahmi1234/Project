# Rencana Fitur

---

## Fitur 1 — Autentikasi dan Manajemen Pengguna (Login/Register)

**Role Penanggung Jawab:** `Backend Developer`

**Sumber Data:** `Internal System (PostgreSQL via Supabase)`

**Deskripsi & Ekspektasi:**
Sistem autentikasi menggunakan JSON Web Token (JWT). Fitur ini memungkinkan pengguna untuk membuat akun dan login agar dapat menyimpan preferensi personal mereka. Diharapkan API dapat memvalidasi kredensial dengan aman dan mengembalikan token yang akan disimpan oleh klien (Frontend).

**Package yang Digunakan:**

- `axios` — HTTP client untuk komunikasi API autentikasi ke backend Laravel
- `zustand` — manajemen state global untuk status autentikasi pengguna
- `react-hot-toast` — menampilkan notifikasi sukses/gagal saat login dan register

---

## Fitur 2 — Dashboard Cuaca Terkini (Current Weather)

**Role Penanggung Jawab:** `Frontend Developer`

**Sumber Data:** `Third-Party API — Open-Meteo (via Laravel Backend)`

**Deskripsi & Ekspektasi:**
Fitur ini menampilkan metrik cuaca saat ini (suhu, kelembapan, kecepatan angin) sebagai bagian dari **Floating Weather Panel** bergaya glassmorphism yang muncul di atas peta ketika pengguna memilih atau men-highlight sebuah wilayah. Panel ini tidak lagi berdiri sebagai halaman dashboard kartu statis, melainkan terintegrasi langsung dengan interaksi peta. Data dikonsumsi dari endpoint Backend Laravel yang sudah memformat ulang respons Open-Meteo.

**Package yang Digunakan:**

- `shadcn/ui` — komponen badge dan separator dalam floating panel
- `lucide-react` — icon cuaca (angin, suhu, kelembapan)
- `framer-motion` — animasi panel muncul/hilang (fade + slide up) saat wilayah dipilih/di-deselect
- `next-themes` — toggle dark/light mode
- `dayjs` — memformat waktu terakhir data diperbarui

---

## Fitur 3 — Prakiraan Cuaca 7 Hari (Weekly Forecast)

**Role Penanggung Jawab:** `Backend Developer`

**Sumber Data:** `Third-Party API — Open-Meteo`

**Deskripsi & Ekspektasi:**
Backend mengambil data prakiraan cuaca selama 7 hari ke depan dari Open-Meteo menggunakan koordinat (latitude & longitude). Backend bertanggung jawab untuk melakukan _caching_ respons secara sementara (menggunakan Redis atau file cache Laravel) untuk mengurangi beban pemanggilan _third-party API_ dan mempercepat waktu respons ke Frontend.

**Package yang Digunakan:**

- `recharts` — menampilkan grafik garis suhu maksimum dan minimum 7 hari
- `shadcn/ui` — komponen card untuk setiap hari dalam forecast
- `dayjs` — memformat nama hari dan tanggal pada kartu prakiraan
- `framer-motion` — animasi slide pada kartu forecast saat pertama dimuat
- `auto-animate` — animasi otomatis saat daftar forecast dirender ulang

---

## Fitur 4 — Pencarian Kota dengan Cinematic Zoom (Geocoding API)

**Role Penanggung Jawab:** `Frontend Developer`

**Sumber Data:** `Third-Party API — Open-Meteo Geocoding API`

**Deskripsi & Ekspektasi:**
Fitur _search bar_ dinamis yang memungkinkan pengguna mencari nama kota di seluruh dunia. Saat pengguna mengetik, Frontend memanggil endpoint Geocoding untuk mendapatkan koordinat kota. Setelah kota dipilih dari hasil pencarian, peta akan menjalankan efek **Cinematic Zoom (`flyTo`)** — animasi terbang dan zoom-in yang mulus ke lokasi tujuan menggunakan Mapbox GL JS. Setelah animasi selesai, wilayah kota otomatis di-highlight dan Floating Weather Panel muncul.

**Alur Interaksi:**

1. User mengetik nama kota → dropdown hasil muncul (debounce 300ms)
2. User memilih kota → `map.flyTo({ center, zoom: 11, duration: 1800 })`
3. Animasi selesai → trigger fetch GeoJSON boundary → render polygon outline
4. Fetch data cuaca terkini + forecast → Floating Panel muncul

**Package yang Digunakan:**

- `mapbox-gl` — metode `flyTo()` dan `fitBounds()` untuk animasi kamera peta
- `shadcn/ui` — komponen input dan dropdown hasil pencarian
- `lucide-react` — icon search dan location pin
- `framer-motion` — animasi dropdown saat hasil pencarian muncul
- `@tanstack/react-query` — manajemen state server, debounce, dan caching hasil pencarian kota

---

## Fitur 5 — Manajemen Lokasi Favorit (Bookmark Locations)

**Role Penanggung Jawab:** `Backend Developer`

**Sumber Data:** `Internal System (PostgreSQL)`

**Deskripsi & Ekspektasi:**
Pengguna yang sudah login dapat menyimpan kota-kota tertentu ke dalam daftar "Lokasi Favorit". Backend menyediakan endpoint CRUD untuk tabel `favorite_locations`. Ketika pengguna **memilih** salah satu lokasi dari daftar favorit, peta akan menjalankan animasi `flyTo` ke koordinat lokasi tersebut — perilaku yang identik dengan memilih hasil pencarian di Fitur 4 — diikuti highlight wilayah dan munculnya Floating Panel.

**Package yang Digunakan:**

- `shadcn/ui` — komponen list dan button untuk daftar favorit
- `lucide-react` — icon bookmark dan trash untuk aksi simpan/hapus
- `auto-animate` — animasi otomatis saat lokasi ditambah atau dihapus dari daftar
- `react-hot-toast` — notifikasi konfirmasi saat lokasi berhasil disimpan atau dihapus

---

## Fitur 6 — Peta Dunia 2D Interaktif (Interactive Map)

**Role Penanggung Jawab:** `Frontend Developer`

**Sumber Data:** `Tile server publik (OpenStreetMap / MapTiler) — tidak memerlukan API key untuk tile dasar`

**Deskripsi & Ekspektasi:**
Antarmuka utama aplikasi diubah dari layout dashboard kartu statis menjadi **peta dunia 2D interaktif berbasis Mapbox GL JS**. Peta mengisi seluruh viewport dan menjadi pusat interaksi pengguna. Pengguna dapat menggeser (pan) dan melakukan zoom pada peta dengan performa mulus (target: 60fps, tidak ada lag pada gestur). Peta menggunakan tile style minimalis yang sesuai dengan tema gelap aplikasi.

**Spesifikasi Teknis:**

- Engine: `mapbox-gl` (WebGL-based, open-source)
- Tile style awal: `https://basemaps.cartocdn.com/gl/dark-matter-gl-style/style.json` (dark, gratis)
- Initial view: `{ center: [113.9213, -0.7893], zoom: 4 }` (terpusat di Indonesia)
- Kontrol peta: zoom button, compass reset (komponen bawaan Mapbox GL JS)

**Package yang Digunakan:**

- `mapbox-gl` — core engine rendering peta
- `react-map-gl` — React wrapper deklaratif untuk Mapbox GL JS
- `zustand` — manajemen state global untuk interaksi dan status koordinat peta

---

## Fitur 7 — Highlight Wilayah & Floating Weather Panel

**Role Penanggung Jawab:** `Frontend Developer` (render) + `Backend Developer` (endpoint GeoJSON)

**Sumber Data:** `Nominatim API — OpenStreetMap (via Laravel Backend proxy)`

**Deskripsi & Ekspektasi:**
Ketika pengguna memilih kota (via search atau favorit), peta menampilkan **garis batas wilayah (polygon outline)** yang menyorot bentuk area kota tersebut secara presisi menggunakan data GeoJSON dari Nominatim. Bersamaan dengan itu, sebuah **Floating Weather Panel** bergaya glassmorphism muncul di atas peta, menggabungkan data Cuaca Terkini (Fitur 2) dan Prakiraan 7 Hari (Fitur 3) dalam satu tampilan terintegrasi. Panel menghilang ketika pengguna mengklik area kosong di peta atau menutupnya secara manual.

**Spesifikasi Teknis — Polygon Outline:**

- Source type: `GeoJSON` layer di Mapbox GL JS
- Layer type: `line` (bukan fill) dengan warna brand `#38BDF8`, lebar 2px
- Tambahan layer `fill` semi-transparan (`rgba(56, 189, 248, 0.08)`) untuk efek highlight area

**Spesifikasi Teknis — Floating Panel:**

- Posisi: kanan bawah peta, `position: fixed`, offset `bottom-6 right-6`
- Style: glassmorphism (`backdrop-blur-md`, `bg-white/10` atau `bg-black/30`, `border border-white/20`)
- Konten: tab atau scroll — bagian atas Cuaca Terkini, bagian bawah grafik Forecast 7 Hari
- Animasi: `framer-motion` fade + slide-up saat muncul, fade + slide-down saat hilang

**Package yang Digunakan:**

- `mapbox-gl` — `addSource`, `addLayer` untuk render GeoJSON polygon
- `react-map-gl` — hook `useMap` untuk akses instance peta
- `framer-motion` — animasi kemunculan Floating Panel
- `recharts` — grafik forecast di dalam Floating Panel
- `shadcn/ui` — layout dan komponen teks dalam panel
- `dayjs` — format tanggal pada kartu forecast dalam panel

# Product Requirements Document (PRD)
# AeroCast — Weather & Environment Dashboard

---

**Versi Dokumen:** `1.0`
**Tanggal:** `2025`
**Status:** `Draft`
**Kelompok:** `OpenSky`
**Repositori:** `https://github.com/Zulfahmi1234/Project`

---

## Daftar Isi

1. [Ringkasan Eksekutif](#1-ringkasan-eksekutif)
2. [Latar Belakang & Masalah](#2-latar-belakang--masalah)
3. [Tujuan Produk](#3-tujuan-produk)
4. [Pengguna & Persona](#4-pengguna--persona)
5. [Ruang Lingkup](#5-ruang-lingkup)
6. [Arsitektur & Stack Teknologi](#6-arsitektur--stack-teknologi)
7. [Spesifikasi Fitur](#7-spesifikasi-fitur)
8. [Persyaratan Non-Fungsional](#8-persyaratan-non-fungsional)
9. [Desain & UI/UX](#9-desain--uiux)
10. [Alur Data](#10-alur-data)
11. [Keamanan](#11-keamanan)
12. [Infrastruktur & Deployment](#12-infrastruktur--deployment)
13. [Risiko & Mitigasi](#13-risiko--mitigasi)
14. [Tim & Tanggung Jawab](#14-tim--tanggung-jawab)

---

## 1. Ringkasan Eksekutif

AeroCast adalah aplikasi web *dashboard* cuaca dan lingkungan yang memungkinkan pengguna memantau kondisi cuaca terkini, melihat prakiraan 7 hari ke depan, mencari kota di seluruh dunia, serta menyimpan lokasi favorit. Aplikasi ini dibangun menggunakan arsitektur *decoupled* antara Frontend (Next.js) dan Backend (Laravel 13), dengan data cuaca bersumber dari Open-Meteo API yang bersifat gratis dan terbuka.

---

## 2. Latar Belakang & Masalah

### 2.1 Latar Belakang

Informasi cuaca merupakan kebutuhan sehari-hari masyarakat, baik untuk perencanaan aktivitas, perjalanan, maupun pertanian. Namun banyak aplikasi cuaca yang tersedia saat ini memiliki antarmuka yang padat iklan, memerlukan biaya berlangganan, atau tidak menyediakan data yang relevan secara lokal untuk wilayah Indonesia.

### 2.2 Pernyataan Masalah

| No | Masalah | Dampak |
|----|---------|--------|
| 1 | Pengguna tidak dapat menyimpan lokasi favorit secara persisten lintas sesi | Harus mencari ulang kota setiap kali membuka aplikasi |
| 2 | Antarmuka aplikasi cuaca umum terlalu kompleks dan penuh iklan | Pengalaman pengguna yang buruk |
| 3 | Data cuaca tidak dipersonalisasi berdasarkan preferensi pengguna | Informasi yang ditampilkan tidak selalu relevan |
| 4 | Tidak ada agregasi data prakiraan mingguan yang mudah dibaca | Sulit merencanakan aktivitas jangka pendek |

---

## 3. Tujuan Produk

### 3.1 Tujuan Utama

- Menyediakan dashboard cuaca yang **minimalis, cepat, dan responsif** berbasis web
- Memungkinkan pengguna **menyimpan preferensi lokasi** secara persisten menggunakan akun
- Menjadi **gateway terpusat** untuk data cuaca melalui backend Laravel sebagai proxy Open-Meteo

### 3.2 Kriteria Keberhasilan (Success Metrics)

| Metrik | Target |
|--------|--------|
| Waktu muat halaman utama | < 2 detik |
| Waktu respons API backend | < 500ms (cached), < 2s (non-cached) |
| Fitur utama berjalan tanpa error | 5 dari 5 fitur |
| Deployment berhasil via CI/CD | Pipeline hijau pada setiap push ke `main` |

---

## 4. Pengguna & Persona

### 4.1 Target Pengguna

Aplikasi ini ditujukan untuk pengguna umum yang membutuhkan informasi cuaca harian secara cepat dan mudah, khususnya:

- Mahasiswa dan pelajar yang merencanakan aktivitas harian
- Masyarakat umum di wilayah Indonesia dan sekitarnya
- Pengguna yang membutuhkan data cuaca multi-kota secara bersamaan

### 4.2 Persona

**Persona 1 — Budi, Mahasiswa (22 tahun)**
- Sering berpindah kota untuk KKN dan magang
- Ingin melihat cuaca beberapa kota sekaligus tanpa harus mencari ulang
- Mengakses via browser laptop dan handphone
- Frustrasi dengan iklan di aplikasi cuaca populer

**Persona 2 — Ibu Sari, Ibu Rumah Tangga (38 tahun)**
- Menggunakan informasi cuaca untuk merencanakan kegiatan keluarga
- Ingin antarmuka yang sederhana dan mudah dibaca
- Hanya butuh suhu, kondisi cuaca, dan prakiraan beberapa hari ke depan

---

## 5. Ruang Lingkup

### 5.1 Dalam Lingkup (In Scope)

- Sistem autentikasi pengguna (register, login, logout)
- Dashboard cuaca terkini berdasarkan koordinat
- Prakiraan cuaca 7 hari
- Pencarian kota via Geocoding API
- Manajemen lokasi favorit (CRUD)
- Caching respons API cuaca di sisi backend
- Deployment ke Render via GitHub Actions

### 5.2 Di Luar Lingkup (Out of Scope)

- Notifikasi push / email untuk peringatan cuaca ekstrem
- Data kualitas udara (AQI) secara real-time
- Aplikasi mobile native (iOS/Android)
- Fitur sosial (berbagi lokasi antar pengguna)
- Dukungan multi-bahasa

---

## 6. Arsitektur & Stack Teknologi

### 6.1 Arsitektur Sistem

Aplikasi menggunakan arsitektur **Client-Server Decoupled**, di mana Frontend dan Backend berjalan sebagai layanan independen yang berkomunikasi via REST API.

```
[Pengguna / Browser]
        │
        ▼
[Frontend — Next.js]  ──── REST API ────►  [Backend — Laravel 13]
                                                     │
                                          ┌──────────┴──────────┐
                                          ▼                     ▼
                                   [PostgreSQL]         [Open-Meteo API]
                                   (via Supabase)       (Third-Party)
```

### 6.2 Stack Teknologi

#### Frontend
| Kategori | Teknologi |
|----------|-----------|
| Framework | Next.js (React) |
| Styling | Tailwind CSS v4 |
| State & Fetching | React Query |
| Auth Client | Supabase Auth |
| Komponen UI | shadcn/ui, Radix UI, Headless UI |
| Animasi | Framer Motion, Auto-Animate, Tailwindcss-Animate |
| Icon | Lucide React |
| Chart | Recharts |
| Utilitas | Day.js, React Hot Toast, Next Themes |

#### Backend
| Kategori | Teknologi |
|----------|-----------|
| Framework | Laravel 13 |
| API Style | REST API |
| Autentikasi | Laravel Sanctum (JWT-based) |
| HTTP Client | Laravel HTTP Client (Guzzle wrapper) |
| Arsitektur | Service Container, Service Provider, Facades |
| Caching | Redis / Laravel File Cache |
| Database | PostgreSQL (via Supabase) |

#### DevOps & Infrastruktur
| Kategori | Teknologi |
|----------|-----------|
| Version Control | GitHub |
| CI/CD | GitHub Actions |
| Hosting | Render |

---

## 7. Spesifikasi Fitur

### Fitur 1 — Autentikasi dan Manajemen Pengguna

**Prioritas:** `Tinggi (P0)`
**PIC:** Backend Developer (Muhammad Naufal Tiftazani)

#### Deskripsi
Sistem autentikasi berbasis token menggunakan Laravel Sanctum. Pengguna dapat membuat akun baru dan login untuk mengakses fitur personal (lokasi favorit).

#### User Stories

| ID | Sebagai | Saya ingin | Agar |
|----|---------|-----------|------|
| US-01 | Pengguna baru | Mendaftar dengan email dan password | Dapat menyimpan preferensi lokasi |
| US-02 | Pengguna terdaftar | Login ke akun saya | Mengakses lokasi favorit yang tersimpan |
| US-03 | Pengguna yang sudah login | Logout dari aplikasi | Sesi saya berakhir dengan aman |

#### Acceptance Criteria

- [ ] Pengguna dapat mendaftar dengan nama, email, dan password (min. 8 karakter)
- [ ] Email yang sudah terdaftar tidak dapat didaftarkan ulang
- [ ] Login mengembalikan Bearer token yang valid
- [ ] Token disimpan di sisi client dan dikirim pada setiap request ke endpoint privat
- [ ] Logout menghapus token dari database Sanctum
- [ ] Semua password di-hash menggunakan bcrypt sebelum disimpan

#### Endpoint Terkait
- `POST /api/v1/auth/register`
- `POST /api/v1/auth/login`
- `POST /api/v1/auth/logout`

---

### Fitur 2 — Dashboard Cuaca Terkini

**Prioritas:** `Tinggi (P0)`
**PIC:** Frontend Developer (Ikhlassul Amal)

#### Deskripsi
Halaman utama yang menampilkan kondisi cuaca terkini berdasarkan lokasi default (Banda Aceh) atau lokasi yang dipilih pengguna. Data diambil dari Open-Meteo API melalui backend Laravel.

#### User Stories

| ID | Sebagai | Saya ingin | Agar |
|----|---------|-----------|------|
| US-04 | Pengguna | Melihat suhu, kelembapan, dan kecepatan angin saat ini | Mengetahui kondisi cuaca terkini |
| US-05 | Pengguna | Melihat ikon kondisi cuaca (cerah, hujan, berawan) | Memahami cuaca secara visual dengan cepat |
| US-06 | Pengguna | Dashboard otomatis memperbarui data saat lokasi berubah | Tidak perlu refresh halaman manual |

#### Acceptance Criteria

- [ ] Menampilkan: suhu (°C), feels like, kelembapan (%), kecepatan angin (km/h), kondisi cuaca, waktu lokal
- [ ] Data diperbarui otomatis saat pengguna berpindah lokasi via search
- [ ] Antarmuka responsif di desktop dan mobile
- [ ] Menampilkan indikator loading saat data sedang diambil
- [ ] Menampilkan pesan error yang informatif jika gagal mengambil data

#### Package Frontend yang Digunakan
- `shadcn/ui` — card dan badge metrik cuaca
- `lucide-react` — icon suhu, angin, kelembapan
- `framer-motion` — animasi transisi saat data diperbarui
- `next-themes` — toggle dark/light mode
- `dayjs` — format waktu terakhir diperbarui

#### Endpoint Terkait
- `GET /api/v1/weather/current?latitude=&longitude=&city_name=`

---

### Fitur 3 — Prakiraan Cuaca 7 Hari

**Prioritas:** `Tinggi (P0)`
**PIC:** Backend Developer (Muhammad Naufal Tiftazani)

#### Deskripsi
Menampilkan prakiraan cuaca harian selama 7 hari ke depan. Backend mengambil data dari Open-Meteo dan melakukan caching untuk efisiensi.

#### User Stories

| ID | Sebagai | Saya ingin | Agar |
|----|---------|-----------|------|
| US-07 | Pengguna | Melihat prakiraan cuaca 7 hari ke depan | Dapat merencanakan aktivitas jangka pendek |
| US-08 | Pengguna | Melihat suhu maksimum dan minimum tiap hari | Mengetahui rentang suhu harian |
| US-09 | Pengguna | Melihat kemungkinan hujan tiap hari | Mempersiapkan diri sebelum beraktivitas |

#### Acceptance Criteria

- [ ] Menampilkan 7 hari prakiraan dalam format kartu harian
- [ ] Setiap kartu menampilkan: nama hari, kondisi cuaca, suhu maks/min, probabilitas hujan, kecepatan angin maks
- [ ] Backend melakukan caching respons dengan TTL 1 jam
- [ ] Response menyertakan field `cached: true/false` dan `cache_expires_at`
- [ ] Grafik garis suhu maks/min 7 hari ditampilkan menggunakan Recharts

#### Package Frontend yang Digunakan
- `recharts` — grafik garis suhu 7 hari
- `shadcn/ui` — card prakiraan harian
- `dayjs` — format nama hari dan tanggal
- `framer-motion` — animasi slide kartu saat pertama dimuat
- `auto-animate` — animasi saat daftar dirender ulang

#### Endpoint Terkait
- `GET /api/v1/weather/forecast?latitude=&longitude=&city_name=`

---

### Fitur 4 — Pencarian Kota (Geocoding)

**Prioritas:** `Tinggi (P0)`
**PIC:** Frontend Developer (Ikhlassul Amal)

#### Deskripsi
Search bar dinamis yang memungkinkan pengguna mencari kota di seluruh dunia. Hasil pencarian berupa koordinat yang digunakan untuk memperbarui data cuaca.

#### User Stories

| ID | Sebagai | Saya ingin | Agar |
|----|---------|-----------|------|
| US-10 | Pengguna | Mencari nama kota via search bar | Melihat cuaca kota yang saya inginkan |
| US-11 | Pengguna | Melihat dropdown hasil pencarian saat mengetik | Memilih kota yang tepat dengan mudah |
| US-12 | Pengguna | Hasil pencarian muncul secara dinamis | Tidak perlu menekan tombol Enter/Search |

#### Acceptance Criteria

- [ ] Pencarian berjalan secara dinamis (debounced, minimal 2 karakter)
- [ ] Dropdown menampilkan maks. 5 hasil pencarian dengan nama kota, provinsi, dan negara
- [ ] Memilih kota dari dropdown memperbarui seluruh data cuaca di dashboard
- [ ] Menampilkan pesan "Kota tidak ditemukan" jika hasil kosong
- [ ] Input search dapat di-clear dengan satu klik

#### Package Frontend yang Digunakan
- `shadcn/ui` — komponen input dan dropdown
- `lucide-react` — icon search dan location pin
- `framer-motion` — animasi dropdown muncul/menghilang
- `react-query` — debounce dan caching hasil pencarian

#### Endpoint Terkait
- `GET /api/v1/geocoding/search?q=&count=`

---

### Fitur 5 — Manajemen Lokasi Favorit

**Prioritas:** `Sedang (P1)`
**PIC:** Backend Developer (Muhammad Naufal Tiftazani)

#### Deskripsi
Pengguna yang sudah login dapat menyimpan kota ke daftar favorit. Daftar ini persisten dan ditampilkan setiap kali pengguna login kembali.

#### User Stories

| ID | Sebagai | Saya ingin | Agar |
|----|---------|-----------|------|
| US-13 | Pengguna login | Menyimpan kota ke daftar favorit | Tidak perlu mencari ulang kota yang sering saya cek |
| US-14 | Pengguna login | Melihat daftar kota favorit saya | Langsung akses cuaca kota tersebut |
| US-15 | Pengguna login | Menghapus kota dari favorit | Daftar saya tetap relevan |

#### Acceptance Criteria

- [ ] Pengguna yang belum login tidak dapat mengakses fitur ini (redirect ke login)
- [ ] Satu pengguna tidak dapat menyimpan kota yang sama dua kali (duplikasi dicegah)
- [ ] Daftar favorit dimuat otomatis saat pengguna login
- [ ] Menghapus lokasi favorit memerlukan konfirmasi sebelum dieksekusi
- [ ] Maksimum 10 lokasi favorit per pengguna

#### Package Frontend yang Digunakan
- `shadcn/ui` — list dan button aksi
- `lucide-react` — icon bookmark dan trash
- `auto-animate` — animasi saat item ditambah/dihapus
- `react-hot-toast` — notifikasi konfirmasi aksi

#### Endpoint Terkait
- `GET /api/v1/favorites`
- `POST /api/v1/favorites`
- `DELETE /api/v1/favorites/{id}`

---

## 8. Persyaratan Non-Fungsional

### 8.1 Performa

| Persyaratan | Target |
|-------------|--------|
| First Contentful Paint (FCP) | < 1.5 detik |
| Time to Interactive (TTI) | < 3 detik |
| Respons API (cached) | < 500ms |
| Respons API (non-cached) | < 2 detik |
| Ukuran bundle JavaScript | < 200KB (gzipped) |

### 8.2 Ketersediaan

- Uptime target: **99%** (sesuai kapasitas Render free tier)
- Aplikasi harus tetap menampilkan data cache lama jika Open-Meteo API tidak dapat dijangkau

### 8.3 Skalabilitas

- Backend Laravel mampu menangani minimal **50 request concurrent** tanpa degradasi performa signifikan
- Caching Redis/file mengurangi beban ke Open-Meteo API hingga **80%** pada trafik normal

### 8.4 Kompatibilitas Browser

| Browser | Versi Minimum |
|---------|---------------|
| Google Chrome | 110+ |
| Mozilla Firefox | 110+ |
| Safari | 15+ |
| Microsoft Edge | 110+ |

### 8.5 Responsivitas

- Mendukung tampilan **mobile** (320px), **tablet** (768px), dan **desktop** (1280px+)
- Menggunakan Tailwind CSS breakpoints: `sm`, `md`, `lg`, `xl`

---

## 9. Desain & UI/UX

### 9.1 Prinsip Desain

- **Minimalis** — hanya menampilkan informasi yang relevan, tanpa elemen dekoratif berlebihan
- **Informatif** — setiap metrik cuaca disertai label dan satuan yang jelas
- **Responsif** — tampilan optimal di semua ukuran layar
- **Aksesibel** — kontras warna memenuhi standar WCAG AA

### 9.2 Tema

- Mendukung **Dark Mode** dan **Light Mode** via `next-themes`
- Warna primer disesuaikan dengan nuansa langit dan cuaca (biru, abu, putih)

### 9.3 Komponen Utama

| Komponen | Deskripsi |
|----------|-----------|
| Weather Card | Menampilkan suhu, kondisi, dan metrik utama kota aktif |
| Forecast Strip | Daftar horizontal 7 kartu prakiraan harian |
| Temperature Chart | Grafik garis suhu maks/min 7 hari (Recharts) |
| Search Bar | Input dengan dropdown hasil Geocoding |
| Favorites Panel | Sidebar/drawer daftar lokasi tersimpan |
| Auth Modal | Form login/register dalam modal overlay |

---

## 10. Alur Data

### 10.1 Alur Autentikasi

```
[Frontend] ──POST /auth/login──► [Backend Laravel]
                                        │
                                 Validasi kredensial
                                        │
                                 Generate Sanctum Token
                                        │
◄──────── Bearer Token ─────────────────┘
[Frontend menyimpan token di memory/cookie]
```

### 10.2 Alur Pengambilan Data Cuaca

```
[Frontend] ──GET /weather/current──► [Backend Laravel]
                                            │
                                     Cek Cache (Redis/File)
                                     ┌──────┴──────┐
                                  Cache HIT      Cache MISS
                                     │               │
                                     │        GET Open-Meteo API
                                     │               │
                                     │        Simpan ke Cache
                                     └──────┬──────┘
                                     Format Response
                                            │
◄──────── JSON Response ────────────────────┘
[Frontend render UI]
```

### 10.3 Alur Pencarian Kota

```
[Pengguna mengetik] ──debounce 300ms──►
[Frontend] ──GET /geocoding/search?q=──► [Backend Laravel]
                                                │
                                     GET Open-Meteo Geocoding API
                                                │
◄──────── Daftar kota + koordinat ──────────────┘
[Frontend tampilkan dropdown]
[Pengguna pilih kota]
[Frontend trigger GET /weather/current dengan koordinat baru]
```

---

## 11. Keamanan

### 11.1 Autentikasi & Autorisasi

- Semua endpoint privat dilindungi middleware `auth:sanctum`
- Token bersifat **per-device** — logout hanya menghapus token perangkat aktif
- Password di-hash menggunakan **bcrypt** dengan cost factor default Laravel

### 11.2 Proteksi Data

- Tidak menyimpan data sensitif pengguna selain email, nama, dan password hash
- Komunikasi antara Frontend dan Backend menggunakan **HTTPS**
- Environment variable (API key, DB credentials) tidak di-commit ke repositori (`.env` di `.gitignore`)

### 11.3 Validasi Input

- Seluruh input pengguna divalidasi di sisi backend menggunakan Laravel Form Request
- Query parameter API (latitude, longitude) divalidasi tipe dan rentang nilainya
- Proteksi terhadap SQL Injection menggunakan Eloquent ORM (parameterized queries)

### 11.4 Rate Limiting

- Endpoint autentikasi dibatasi **10 request/menit** per IP untuk mencegah brute force
- Endpoint cuaca dibatasi **60 request/menit** per pengguna

---

## 12. Infrastruktur & Deployment

### 12.1 Lingkungan

| Lingkungan | Deskripsi |
|------------|-----------|
| Development | Lokal di mesin masing-masing developer |
| Production | Render (Frontend + Backend sebagai service terpisah) |

### 12.2 CI/CD Pipeline (GitHub Actions)

```
Push ke branch main
        │
        ▼
[GitHub Actions trigger]
        │
   ┌────┴────┐
   ▼         ▼
[Lint &   [Run Tests]
 Build]        │
   │      Pass / Fail
   └────┬────┘
        ▼
  [Deploy ke Render]
  (Frontend & Backend)
```

### 12.3 Struktur Repositori

```
Project/
├── Instruksi-Tugas/
│   ├── 01-identitas-kelompok.md
│   ├── 02-rencana-fitur.md
│   ├── 03-api-spec.md
│   └── 04-prd.md
└── backend/
    ├── app/
    ├── routes/
    ├── database/
    └── ...
```

---

## 13. Risiko & Mitigasi

| No | Risiko | Kemungkinan | Dampak | Mitigasi |
|----|--------|-------------|--------|----------|
| 1 | Open-Meteo API down atau rate limit tercapai | Sedang | Tinggi | Implementasi caching agresif (TTL 1 jam), tampilkan data cache lama jika API gagal |
| 2 | Token kedaluwarsa menyebabkan pengguna tidak bisa akses | Sedang | Sedang | Implementasi refresh token atau redirect otomatis ke halaman login |
| 3 | Render free tier memiliki cold start (delay ~30 detik) | Tinggi | Sedang | Tampilkan loading skeleton yang informatif di Frontend |
| 4 | Konflik dependensi antara Tailwind CSS v4 dan shadcn/ui | Sedang | Sedang | Pin versi package di `package.json`, uji integrasi di awal pengembangan |
| 5 | Data koordinat tidak akurat dari Geocoding API | Rendah | Sedang | Tampilkan nama lengkap kota (kota, provinsi, negara) di dropdown agar pengguna dapat memilih dengan tepat |

---

## 14. Tim & Tanggung Jawab

| Nama | NIM | Role | Tanggung Jawab Utama |
|------|-----|------|----------------------|
| Ikhlassul Amal | 230705105 | Frontend Developer | Implementasi UI, integrasi React Query, komponen shadcn/ui, animasi Framer Motion |
| Muhammad Naufal Tiftazani | 230705117 | Backend Developer | REST API Laravel, autentikasi Sanctum, proxy Open-Meteo, caching Redis |
| Zulfahmi | 230705116 | DevOps Engineer | GitHub Actions CI/CD, konfigurasi Render, manajemen environment variable |

---

*Dokumen ini bersifat living document dan akan diperbarui seiring perkembangan proyek.*

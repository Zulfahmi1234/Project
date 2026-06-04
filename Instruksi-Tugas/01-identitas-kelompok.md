# Identitas Kelompok

---

**Nama Kelompok:** Kelompok OpenSky

**Nama Proyek / Aplikasi:** AeroCast - Weather & Environment Dashboard

**Jumlah Anggota:** 3 orang

**Repositori:** `https://github.com/Zulfahmi1234/Project`

---

## Anggota & Role

**Anggota 1**
- Nama Lengkap: `Ikhlassul Amal`
- NIM: `230705105`
- Role: `Frontend Developer`
- Teknologi: `Next.js, Tailwind CSS v4, React Query, Supabase Auth`

**Anggota 2**
- Nama Lengkap: `Muhammad Naufal Tiftazani`
- NIM: `230705117`
- Role: `Backend Developer`
- Teknologi: `Laravel 13, REST API, JWT Authentication`

**Anggota 3**
- Nama Lengkap: `Zulfahmi`
- NIM: `230705116`
- Role: `DevOps Engineer`
- Teknologi: `GitHub, Render`

---

## Stack Teknologi

**Frontend:** `Next.js (React), Supabase Auth`

**Backend:** `Laravel 13` *(wajib)*

**Database:** `PostgreSQL (via Supabase)`

**DevOps / Infrastruktur:** `GitHub Actions (untuk CI/CD pipeline), Render`

---

## Package Frontend

**Animasi & Transisi UI**
- `framer-motion` — animasi kompleks pada komponen UI
- `auto-animate` — animasi otomatis untuk list dan card
- `tailwindcss-animate` — animasi ringan via Tailwind className

**Komponen Siap Pakai**
- `shadcn/ui` — komponen utama berbasis Tailwind CSS v4
- `radix-ui` — headless component, basis dari shadcn/ui
- `headlessui` — komponen accessible buatan Tailwind team

**Icon Library**
- `lucide-react` — icon set konsisten, terintegrasi dengan shadcn/ui

**Chart & Data Visualisasi**
- `recharts` — grafik suhu, kelembapan, dan kecepatan angin

**Utility**
- `dayjs` — manipulasi tanggal dan waktu untuk data prakiraan cuaca
- `react-hot-toast` — notifikasi feedback login, error, dan aksi pengguna
- `next-themes` — dark/light mode toggle untuk dashboard

---

## Arsitektur Aplikasi

Aplikasi ini menggunakan arsitektur *Client-Server* yang terpisah (*decoupled*), di mana Frontend dan Backend berjalan sebagai layanan independen.

**Aplikasi 1 — Frontend**
- Nama Aplikasi: `AeroCast Web Client`
- Deskripsi Singkat: `Aplikasi antarmuka pengguna berbasis SPA (Single Page Application) dengan desain minimalis untuk menampilkan data cuaca dan mengelola preferensi pengguna.`
- Berkomunikasi dengan: `Aplikasi 2 (Backend Laravel) via REST API.`

**Aplikasi 2 — Backend (Laravel)**
- Nama Aplikasi / Service: `AeroCast API Gateway & Core Service`
- Deskripsi Singkat: `Layanan backend yang menangani autentikasi pengguna, manajemen database (lokasi favorit), dan bertindak sebagai proxy/gateway untuk mengambil data dari Open-Meteo API.`
- Menyediakan layanan untuk: `Aplikasi 1 (Frontend) dan mengelola komunikasi dengan Open-Meteo (Third-Party API).`
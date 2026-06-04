# Identitas Kelompok

---

**Nama Kelompok:** Kelompok OpenSky

**Nama Proyek / Aplikasi:** AeroCast - Weather & Environment Dashboard

**Jumlah Anggota:** 3 orang

**Repositori:** `https://github.com/Zulfahmi1234/Project`

---

## Anggota & Role

**Anggota 1**
- Nama Lengkap: `Muhammad Naufal Tiftazani`
- NIM: `230705117`
- Role: `Frontend Developer`
- Teknologi: `Next.js, Tailwind CSS v4, React Query`

**Anggota 2**
- Nama Lengkap: `Ikhlassul Amal`
- NIM: `230705105`
- Role: `Backend Developer`
- Teknologi: `Laravel 11, REST API, JWT Authentication`

**Anggota 3**
- Nama Lengkap: `Zulfahmi`
- NIM: `230705116`
- Role: `DevOps Engineer`
- Teknologi: `Docker, Nginx, GitHub Actions, Linux VPS`

---

## Stack Teknologi

**Frontend:** `Next.js (React)`

**Backend:** `Laravel` *(wajib)*

**Database:** `PostgreSQL`

**DevOps / Infrastruktur:** `Docker (untuk containerization), GitHub Actions (untuk CI/CD pipeline)`

---

## Arsitektur Aplikasi

Aplikasi ini menggunakan arsitektur *Client-Server* yang terpisah (*decoupled*), di mana Frontend dan Backend berjalan sebagai layanan independen dalam *container* Docker.

**Aplikasi 1 — Frontend**
- Nama Aplikasi: `AeroCast Web Client`
- Deskripsi Singkat: `Aplikasi antarmuka pengguna berbasis SPA (Single Page Application) dengan desain minimalis untuk menampilkan data cuaca dan mengelola preferensi pengguna.`
- Berkomunikasi dengan: `Aplikasi 2 (Backend Laravel) via REST API.`

**Aplikasi 2 — Backend (Laravel)**
- Nama Aplikasi / Service: `AeroCast API Gateway & Core Service`
- Deskripsi Singkat: `Layanan backend yang menangani autentikasi pengguna, manajemen database (lokasi favorit), dan bertindak sebagai proxy/gateway untuk mengambil data dari Open-Meteo API.`
- Menyediakan layanan untuk: `Aplikasi 1 (Frontend) dan mengelola komunikasi dengan Open-Meteo (Third-Party API).`
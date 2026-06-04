# Rencana Fitur

---

## Fitur 1 — Autentikasi dan Manajemen Pengguna (Login/Register)

**Role Penanggung Jawab:** `Backend Developer`

**Sumber Data:** `Internal System (PostgreSQL)`

**Deskripsi & Ekspektasi:**
Sistem autentikasi menggunakan JSON Web Token (JWT). Fitur ini memungkinkan pengguna untuk membuat akun dan login agar dapat menyimpan preferensi personal mereka. Diharapkan API dapat memvalidasi kredensial dengan aman dan mengembalikan token yang akan disimpan oleh klien (Frontend).

---

## Fitur 2 — Dashboard Cuaca Terkini (Current Weather)

**Role Penanggung Jawab:** `Frontend Developer`

**Sumber Data:** `Third-Party API — Open-Meteo (via Laravel Backend)`

**Deskripsi & Ekspektasi:**
Fitur ini menampilkan metrik cuaca saat ini (suhu, kelembapan, kecepatan angin) berdasarkan lokasi default (misal: Banda Aceh) atau lokasi pengguna. Frontend akan merender antarmuka minimalis dan responsif, mengonsumsi data yang sudah diformat ulang oleh Backend Laravel dari Open-Meteo agar sesuai dengan kebutuhan UI.

---

## Fitur 3 — Prakiraan Cuaca 7 Hari (Weekly Forecast)

**Role Penanggung Jawab:** `Backend Developer`

**Sumber Data:** `Third-Party API — Open-Meteo`

**Deskripsi & Ekspektasi:**
Backend mengambil data prakiraan cuaca selama 7 hari ke depan dari Open-Meteo menggunakan koordinat (latitude & longitude). Backend bertanggung jawab untuk melakukan *caching* respons secara sementara (menggunakan Redis atau file cache Laravel) untuk mengurangi beban pemanggilan *third-party API* dan mempercepat waktu respons ke Frontend.

---

## Fitur 4 — Pencarian Kota (Geocoding API)

**Role Penanggung Jawab:** `Frontend Developer`

**Sumber Data:** `Third-Party API — Open-Meteo Geocoding API`

**Deskripsi & Ekspektasi:**
Fitur *search bar* dinamis yang memungkinkan pengguna mencari nama kota di seluruh dunia. Saat pengguna mengetik, Frontend akan memanggil *endpoint* Geocoding untuk mendapatkan koordinat (latitude & longitude) dari kota tersebut, yang kemudian digunakan untuk memperbarui data pada fitur cuaca.

---

## Fitur 5 — Manajemen Lokasi Favorit (Bookmark Locations)

**Role Penanggung Jawab:** `Backend Developer`

**Sumber Data:** `Internal System (PostgreSQL)`

**Deskripsi & Ekspektasi:**
Pengguna yang sudah login dapat menyimpan kota-kota tertentu ke dalam daftar "Lokasi Favorit". Backend akan menyediakan endpoint CRUD (Create, Read, Update, Delete) untuk tabel `favorite_locations`. Fitur ini memastikan bahwa ketika pengguna login kembali, mereka langsung melihat cuaca dari kota-kota yang telah mereka simpan.
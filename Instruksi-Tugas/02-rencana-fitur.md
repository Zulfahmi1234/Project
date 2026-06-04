# Rencana Fitur

---

## Fitur 1 — Autentikasi dan Manajemen Pengguna (Login/Register)

**Role Penanggung Jawab:** `Backend Developer`

**Sumber Data:** `Internal System (PostgreSQL)`

**Deskripsi & Ekspektasi:**
Sistem autentikasi menggunakan JSON Web Token (JWT). Fitur ini memungkinkan pengguna untuk membuat akun dan login agar dapat menyimpan preferensi personal mereka. Diharapkan API dapat memvalidasi kredensial dengan aman dan mengembalikan token yang akan disimpan oleh klien (Frontend).

**Package yang Digunakan:**
- `react-hot-toast` — menampilkan notifikasi sukses/gagal saat login dan register

---

## Fitur 2 — Dashboard Cuaca Terkini (Current Weather)

**Role Penanggung Jawab:** `Frontend Developer`

**Sumber Data:** `Third-Party API — Open-Meteo (via Laravel Backend)`

**Deskripsi & Ekspektasi:**
Fitur ini menampilkan metrik cuaca saat ini (suhu, kelembapan, kecepatan angin) berdasarkan lokasi default (misal: Banda Aceh) atau lokasi pengguna. Frontend akan merender antarmuka minimalis dan responsif, mengonsumsi data yang sudah diformat ulang oleh Backend Laravel dari Open-Meteo agar sesuai dengan kebutuhan UI.

**Package yang Digunakan:**
- `shadcn/ui` — komponen card dan badge untuk menampilkan metrik cuaca
- `lucide-react` — icon cuaca (angin, suhu, kelembapan)
- `framer-motion` — animasi transisi saat data cuaca diperbarui
- `next-themes` — toggle dark/light mode pada dashboard
- `dayjs` — memformat waktu terakhir data diperbarui

---

## Fitur 3 — Prakiraan Cuaca 7 Hari (Weekly Forecast)

**Role Penanggung Jawab:** `Backend Developer`

**Sumber Data:** `Third-Party API — Open-Meteo`

**Deskripsi & Ekspektasi:**
Backend mengambil data prakiraan cuaca selama 7 hari ke depan dari Open-Meteo menggunakan koordinat (latitude & longitude). Backend bertanggung jawab untuk melakukan *caching* respons secara sementara (menggunakan Redis atau file cache Laravel) untuk mengurangi beban pemanggilan *third-party API* dan mempercepat waktu respons ke Frontend.

**Package yang Digunakan:**
- `recharts` — menampilkan grafik garis suhu maksimum dan minimum 7 hari
- `shadcn/ui` — komponen card untuk setiap hari dalam forecast
- `dayjs` — memformat nama hari dan tanggal pada kartu prakiraan
- `framer-motion` — animasi slide pada kartu forecast saat pertama dimuat
- `auto-animate` — animasi otomatis saat daftar forecast dirender ulang

---

## Fitur 4 — Pencarian Kota (Geocoding API)

**Role Penanggung Jawab:** `Frontend Developer`

**Sumber Data:** `Third-Party API — Open-Meteo Geocoding API`

**Deskripsi & Ekspektasi:**
Fitur *search bar* dinamis yang memungkinkan pengguna mencari nama kota di seluruh dunia. Saat pengguna mengetik, Frontend akan memanggil *endpoint* Geocoding untuk mendapatkan koordinat (latitude & longitude) dari kota tersebut, yang kemudian digunakan untuk memperbarui data pada fitur cuaca.

**Package yang Digunakan:**
- `shadcn/ui` — komponen input dan dropdown hasil pencarian
- `lucide-react` — icon search dan location pin
- `framer-motion` — animasi dropdown saat hasil pencarian muncul
- `react-query` — debounce dan caching hasil pencarian kota

---

## Fitur 5 — Manajemen Lokasi Favorit (Bookmark Locations)

**Role Penanggung Jawab:** `Backend Developer`

**Sumber Data:** `Internal System (PostgreSQL)`

**Deskripsi & Ekspektasi:**
Pengguna yang sudah login dapat menyimpan kota-kota tertentu ke dalam daftar "Lokasi Favorit". Backend akan menyediakan endpoint CRUD (Create, Read, Update, Delete) untuk tabel `favorite_locations`. Fitur ini memastikan bahwa ketika pengguna login kembali, mereka langsung melihat cuaca dari kota-kota yang telah mereka simpan.

**Package yang Digunakan:**
- `shadcn/ui` — komponen list dan button untuk daftar favorit
- `lucide-react` — icon bookmark dan trash untuk aksi simpan/hapus
- `auto-animate` — animasi otomatis saat lokasi ditambah atau dihapus dari daftar
- `react-hot-toast` — notifikasi konfirmasi saat lokasi berhasil disimpan atau dihapus
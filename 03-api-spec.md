# API Specification

---

## 1. Register Pengguna

**Method:** `POST`

**URL:** `/api/v1/auth/register`

**Deskripsi:** `Membuat akun pengguna baru. Data disimpan ke tabel users di PostgreSQL dengan password yang di-hash menggunakan bcrypt.`

**Autentikasi Diperlukan:** `Tidak`

**Sumber:** `Internal System`

**Request Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Request Body:**

```json
{
  "name": "string",
  "email": "string",
  "password": "string",
  "password_confirmation": "string"
}
```

**Response Sukses (`201 Created`):**

```json
{
  "status": "success",
  "message": "Registrasi berhasil.",
  "data": {
    "user": {
      "id": 1,
      "name": "Naufal Tiftazani",
      "email": "naufal@student.ac.id",
      "created_at": "2025-01-01T10:00:00.000000Z"
    },
    "access_token": "1|LaravelSanctumTokenExample...",
    "token_type": "Bearer"
  }
}
```

**Response Gagal (`422 Unprocessable Entity`):**

```json
{
  "status": "error",
  "message": "Validasi gagal.",
  "errors": {
    "email": ["Email sudah terdaftar."],
    "password": ["Password minimal 8 karakter."]
  }
}
```

---

## 2. User Authentication (Login)

**Method:** `POST`

**URL:** `/api/v1/auth/login`

**Deskripsi:** Melakukan autentikasi pengguna dan mengembalikan JWT token untuk akses endpoint privat.

**Autentikasi Diperlukan:** `Tidak`

**Sumber:** `Internal System`

**Request Headers:**

```
Content-Type: application/json
Accept: application/json
```

**Request Body:**

```json
{
  "email": "user@student.ac.id",
  "password": "password123"
}
```

**Response Sukses (`200 OK`):**

```json
{
  "status": "success",
  "message": "Login berhasil.",
  "data": {
    "user": {
      "id": 1,
      "name": "Naufal Tiftazani",
      "email": "user@student.ac.id"
    },
    "access_token": "1|LaravelSanctumTokenExample...",
    "token_type": "Bearer"
  }
}
```

**Response Gagal — Validasi Error (422 Unprocessable Entity):**

```json
{
  "status": "error",
  "message": "Validasi gagal.",
  "errors": {
    "email": ["Format email tidak valid."],
    "password": ["Password wajib diisi."]
  }
}
```

**Response Gagal — Kredensial Salah (401 Unauthorized)**

```json
{
  "status": "error",
  "message": "Validasi gagal.",
  "errors": {
    "email": ["Format email tidak valid."],
    "password": ["Password wajib diisi."]
  }
}
```

---

## 3. Logout Pengguna

**Method:** `POST`

**URL:** `/api/v1/auth/logout`

**Deskripsi:** `Menginvalidasi token aktif pengguna yang sedang login (menghapus token dari database Sanctum).`

**Autentikasi Diperlukan:** `Ya`

**Sumber:** `Internal System`

**Request Headers:**

```
Authorization: Bearer <token>
Content-Type: application/json
Accept: application/json
```

**Request Body:** `-`

**Response Sukses (`200 OK`):**

```json
{
  "status": "success",
  "message": "Logout berhasil. Token telah dihapus."
}
```

**Response Gagal (`401 Unauthorized`):**

```json
{
  "status": "error",
  "message": "Token tidak valid atau sudah kedaluwarsa."
}
```

## 4. Get Current Weather

**Method:** `GET`

**URL:** `/api/v1/weather/current`

**Deskripsi:** `Mengambil data cuaca terkini (suhu, kelembapan, kecepatan angin) berdasarkan koordinat latitude dan longitude. Backend bertindak sebagai proxy yang meneruskan request ke Open-Meteo API, memformat ulang responsnya, lalu mengembalikannya ke Frontend.`

**Autentikasi Diperlukan:** `Ya`

**Sumber:** `Third-Party API — Open-Meteo`

**Request Headers:**

```
Authorization: Bearer <token>
Accept: application/json
```

**Query Parameters:** `?latitude=5.5483&longitude=95.3238&city_name=Banda+Aceh`

**Request Body:** `-`

**Response Sukses (`200 OK`):**

```json
{
  "status": "success",
  "data": {
    "city": "Banda Aceh",
    "latitude": 5.5483,
    "longitude": 95.3238,
    "timezone": "Asia/Jakarta",
    "current": {
      "time": "2025-01-01T10:00",
      "temperature": 31.2,
      "feels_like": 34.5,
      "humidity": 78,
      "wind_speed": 12.4,
      "wind_direction": 180,
      "condition": "Cerah Berawan",
      "is_day": true
    },
    "units": {
      "temperature": "°C",
      "wind_speed": "km/h",
      "humidity": "%"
    }
  }
}
```

**Response Gagal (`400 Bad Request`):**

```json
{
  "status": "error",
  "message": "Parameter latitude dan longitude wajib diisi."
}
```

**Response Gagal (`502 Bad Gateway`):**

```json
{
  "status": "error",
  "message": "Gagal mengambil data dari layanan cuaca eksternal. Coba lagi nanti."
}
```

---

## 5. Get 7-Day Weather Forecast

**Method:** `GET`

**URL:** `/api/v1/weather/forecast`

**Deskripsi:** `Mengambil data prakiraan cuaca 7 hari ke depan berdasarkan koordinat. Backend melakukan caching respons menggunakan Redis atau file cache Laravel (TTL: 1 jam) untuk mengurangi pemanggilan ke Open-Meteo API.`

**Autentikasi Diperlukan:** `Ya`

**Sumber:** `Third-Party API — Open-Meteo`

**Request Headers:**

```
Authorization: Bearer <token>
Accept: application/json
```

**Query Parameters:** `?latitude=5.5483&longitude=95.3238&city_name=Banda+Aceh`

**Request Body:** `-`

**Response Sukses (`200 OK`):**

```json
{
  "status": "success",
  "data": {
    "city": "Banda Aceh",
    "latitude": 5.5483,
    "longitude": 95.3238,
    "cached": true,
    "cache_expires_at": "2025-01-01T11:00:00Z",
    "forecast": [
      {
        "date": "2025-01-01",
        "day_name": "Rabu",
        "condition": "Cerah Berawan",
        "temperature_max": 33.5,
        "temperature_min": 24.1,
        "humidity_mean": 76,
        "wind_speed_max": 18.2,
        "precipitation_sum": 0.0,
        "precipitation_probability_max": 10
      },
      {
        "date": "2025-01-02",
        "day_name": "Kamis",
        "condition": "Hujan Ringan",
        "temperature_max": 29.8,
        "temperature_min": 23.5,
        "humidity_mean": 88,
        "wind_speed_max": 22.0,
        "precipitation_sum": 4.5,
        "precipitation_probability_max": 75
      }
    ],
    "units": {
      "temperature": "°C",
      "wind_speed": "km/h",
      "precipitation": "mm"
    }
  }
}
```

**Response Gagal (`400 Bad Request`):**

```json
{
  "status": "error",
  "message": "Parameter latitude dan longitude wajib diisi."
}
```

**Response Gagal (`502 Bad Gateway`):**

```json
{
  "status": "error",
  "message": "Gagal mengambil data prakiraan dari layanan cuaca eksternal. Coba lagi nanti."
}
```

---

## 6. Search City (Geocoding)

**Method:** `GET`

**URL:** `/api/v1/geocoding/search`

**Deskripsi:** `Mencari koordinat (latitude & longitude) berdasarkan nama kota menggunakan Open-Meteo Geocoding API. Dipanggil secara dinamis saat pengguna mengetik di search bar. Hasilnya digunakan Frontend untuk memperbarui data cuaca.`

**Autentikasi Diperlukan:** `Ya`

**Sumber:** `Third-Party API — Open-Meteo Geocoding API`

**Request Headers:**

```
Authorization: Bearer <token>
Accept: application/json
```

**Query Parameters:** `?q=Banda+Aceh&count=5`

**Request Body:** `-`

**Response Sukses (`200 OK`):**

```json
{
  "status": "success",
  "data": {
    "query": "Banda Aceh",
    "count": 2,
    "results": [
      {
        "id": 1214026,
        "name": "Banda Aceh",
        "latitude": 5.5483,
        "longitude": 95.3238,
        "country": "Indonesia",
        "country_code": "ID",
        "admin1": "Aceh",
        "timezone": "Asia/Jakarta",
        "display_name": "Banda Aceh, Aceh, Indonesia"
      }
    ]
  }
}
```

**Response Gagal (`422 Unprocessable Entity`):**

```json
{
  "status": "error",
  "message": "Parameter pencarian minimal 2 karakter."
}
```

---

## 7. Get All Favorite Locations

**Method:** `GET`

**URL:** `/api/v1/favorites`

**Deskripsi:** `Mengambil seluruh daftar lokasi favorit milik pengguna yang sedang login dari tabel favorite_locations di PostgreSQL.`

**Autentikasi Diperlukan:** `Ya`

**Sumber:** `Internal System`

**Request Headers:**

```
Authorization: Bearer <token>
Accept: application/json
```

**Request Body:** `-`

**Response Sukses (`200 OK`):**

```json
{
  "status": "success",
  "data": {
    "count": 2,
    "favorites": [
      {
        "id": 1,
        "city_name": "Banda Aceh",
        "latitude": 5.5483,
        "longitude": 95.3238,
        "country": "Indonesia",
        "country_code": "ID",
        "timezone": "Asia/Jakarta",
        "created_at": "2025-01-01T09:00:00.000000Z"
      },
      {
        "id": 2,
        "city_name": "Jakarta",
        "latitude": -6.2088,
        "longitude": 106.8456,
        "country": "Indonesia",
        "country_code": "ID",
        "timezone": "Asia/Jakarta",
        "created_at": "2025-01-02T14:30:00.000000Z"
      }
    ]
  }
}
```

**Response Gagal (`401 Unauthorized`):**

```json
{
  "status": "error",
  "message": "Unauthenticated. Silakan login terlebih dahulu."
}
```

---

## 8. Add Favorite Location

**Method:** `POST`

**URL:** `/api/v1/favorites`

**Deskripsi:** `Menyimpan lokasi baru ke daftar favorit pengguna yang sedang login. Mencegah duplikasi lokasi yang sama untuk satu pengguna.`

**Autentikasi Diperlukan:** `Ya`

**Sumber:** `Internal System`

**Request Headers:**

```
Authorization: Bearer <token>
Content-Type: application/json
Accept: application/json
```

**Request Body:**

```json
{
  "city_name": "string",
  "latitude": "float",
  "longitude": "float",
  "country": "string",
  "country_code": "string",
  "timezone": "string"
}
```

**Response Sukses (`201 Created`):**

```json
{
  "status": "success",
  "message": "Lokasi berhasil ditambahkan ke favorit.",
  "data": {
    "id": 3,
    "city_name": "Medan",
    "latitude": 3.5952,
    "longitude": 98.6722,
    "country": "Indonesia",
    "country_code": "ID",
    "timezone": "Asia/Jakarta",
    "created_at": "2025-01-03T08:00:00.000000Z"
  }
}
```

**Response Gagal (`409 Conflict`):**

```json
{
  "status": "error",
  "message": "Lokasi ini sudah ada di daftar favorit Anda."
}
```

**Response Gagal (`422 Unprocessable Entity`):**

```json
{
  "status": "error",
  "message": "Validasi gagal.",
  "errors": {
    "city_name": ["Nama kota wajib diisi."],
    "latitude": ["Latitude wajib diisi."],
    "longitude": ["Longitude wajib diisi."]
  }
}
```

---

## 9. Delete Favorite Location

**Method:** `DELETE`

**URL:** `/api/v1/favorites/{id}`

**Deskripsi:** `Menghapus lokasi dari daftar favorit pengguna berdasarkan ID. Hanya pemilik data yang dapat menghapus (ownership check).`

**Autentikasi Diperlukan:** `Ya`

**Sumber:** `Internal System`

**Request Headers:**

```
Authorization: Bearer <token>
Accept: application/json
```

**Request Body:** `-`

**Response Sukses (`200 OK`):**

```json
{
  "status": "success",
  "message": "Lokasi berhasil dihapus dari favorit."
}
```

**Response Gagal (`404 Not Found`):**

```json
{
  "status": "error",
  "message": "Lokasi favorit tidak ditemukan."
}
```

**Response Gagal (`403 Forbidden`):**

```json
{
  "status": "error",
  "message": "Anda tidak memiliki izin untuk menghapus data ini."
}
```

---

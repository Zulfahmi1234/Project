# PRD — AeroCast Backend (API Gateway & Core Service)

> **Dokumen:** Product Requirements Document — Backend  
> **Proyek:** AeroCast — Weather & Environment Dashboard  
> **Kelompok:** OpenSky  
> **PIC Backend:** Muhammad Naufal Tiftazani (NIM: 230705117)  
> **Versi:** 1.0  
> **Tanggal:** 2026-06-05  

---

## 1. Latar Belakang & Tujuan Produk

AeroCast adalah aplikasi dashboard cuaca berbasis web yang memungkinkan pengguna melihat data cuaca real-time dan prakiraan 7 hari di atas peta dunia 2D interaktif. Backend berperan sebagai **API Gateway & Core Service** — satu-satunya lapisan yang boleh berkomunikasi langsung dengan third-party API (Open-Meteo dan Nominatim), sekaligus mengelola autentikasi pengguna dan data lokasi favorit di PostgreSQL.

### Tujuan Backend

| # | Tujuan |
|---|--------|
| 1 | Menyediakan REST API yang aman (Bearer Token) sebagai satu-satunya antarmuka antara frontend dan dunia luar |
| 2 | Bertindak sebagai proxy untuk Open-Meteo API (cuaca & forecast) dan Nominatim API (GeoJSON boundary) |
| 3 | Mengelola autentikasi pengguna (register, login, logout) menggunakan Laravel Sanctum |
| 4 | Menyimpan dan mengelola lokasi favorit pengguna di PostgreSQL via Supabase |
| 5 | Menerapkan caching (Redis/file cache) untuk mengurangi beban third-party API |

---

## 2. Stakeholder

| Role | Nama | Kepentingan |
|------|------|-------------|
| Backend Developer (PIC) | Muhammad Naufal Tiftazani | Implementasi seluruh backend |
| Frontend Developer | Ikhlassul Amal | Konsumsi semua endpoint API |
| DevOps Engineer | Zulfahmi | Deploy backend ke Render, CI/CD |

---

## 3. Batasan & Asumsi

> [!IMPORTANT]
> Aturan arsitektur yang tidak boleh dilanggar:

- **FE dilarang** memanggil Open-Meteo atau Nominatim secara langsung (kecuali Geocoding search — lihat Catatan Khusus di Fitur 4).
- **Semua response** menggunakan format seragam: `{ "status": "...", "message": "...", "data": {...} }`.
- **Token** disimpan di sisi FE (localStorage/cookie httpOnly), dikirim via header `Authorization: Bearer <token>`.
- **Database:** PostgreSQL via Supabase — Laravel hanya terhubung via standard DB connection, bukan Supabase client SDK.
- **HTTP Client:** Wajib menggunakan Laravel HTTP Client (Guzzle wrapper), bukan Axios atau library lain.
- **Nominatim rate limit:** 1 request/detik — caching 24 jam adalah **wajib**, bukan opsional.

---

## 4. Tech Stack Backend

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 13 (PHP) |
| Auth | Laravel Sanctum (token-based) |
| HTTP Client | Laravel HTTP Client (Guzzle wrapper) |
| Cache Driver | Redis (primary) / File cache (fallback) |
| Database | PostgreSQL via Supabase |
| Arsitektur | Service Container, Service Provider, Facades |
| Hosting | Render (Web Service) |
| CI/CD | GitHub Actions |

---

## 5. Arsitektur Sistem

```
[Next.js Frontend — Mapbox GL JS]
        |
        | REST API (Bearer Token via Authorization header)
        v
[Laravel Backend — AeroCast API Gateway]
        |               |               |
        | DB             | HTTP Proxy     | HTTP Proxy
        v               v               v
  [PostgreSQL]   [Open-Meteo API]  [Nominatim API]
  via Supabase   (cuaca & forecast) (GeoJSON boundary)
```

### Lapisan Arsitektur Backend

```
routes/api.php
    └── Controllers/ (Http layer — validasi request & format response)
            └── Services/ (Business logic — API calls, cache, DB operations)
                    └── Models/ (Eloquent — interaksi DB)
```

> [!NOTE]
> Semua logika bisnis (pemanggilan HTTP, cache, transformasi data) wajib berada di **Services/**, bukan di Controllers. Controllers hanya boleh mendelegasikan ke service dan membentuk response.

---

## 6. Struktur Folder Backend

```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── V1/
│   │   │           ├── AuthController.php
│   │   │           ├── WeatherController.php
│   │   │           ├── GeocodingController.php
│   │   │           └── FavoriteLocationController.php
│   │   └── Requests/
│   │       ├── Auth/
│   │       │   ├── RegisterRequest.php
│   │       │   └── LoginRequest.php
│   │       ├── Weather/
│   │       │   └── GetWeatherRequest.php
│   │       ├── Geocoding/
│   │       │   ├── SearchCityRequest.php
│   │       │   └── GetBoundaryRequest.php
│   │       └── Favorites/
│   │           └── StoreFavoriteRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   └── FavoriteLocation.php
│   └── Services/
│       ├── OpenMeteoService.php
│       ├── NominatimService.php
│       └── FavoriteLocationService.php
├── database/
│   └── migrations/
│       ├── create_users_table.php
│       └── create_favorite_locations_table.php
└── routes/
    └── api.php
```

---

## 7. Skema Database

### Tabel: `users`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | `BIGINT` (PK, auto-increment) | Primary key |
| `name` | `VARCHAR(255)` | Nama lengkap pengguna |
| `email` | `VARCHAR(255)` (unique) | Email pengguna |
| `password` | `VARCHAR(255)` | Hash bcrypt |
| `created_at` | `TIMESTAMP` | Timestamp dibuat |
| `updated_at` | `TIMESTAMP` | Timestamp diperbarui |

### Tabel: `personal_access_tokens` (Sanctum — auto-generated)

Tabel ini dibuat otomatis oleh Laravel Sanctum saat menjalankan `php artisan migrate`.

### Tabel: `favorite_locations`

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| `id` | `BIGINT` (PK, auto-increment) | Primary key |
| `user_id` | `BIGINT` (FK → `users.id`) | Pemilik lokasi |
| `city_name` | `VARCHAR(255)` | Nama kota |
| `latitude` | `DECIMAL(10, 7)` | Koordinat lintang |
| `longitude` | `DECIMAL(10, 7)` | Koordinat bujur |
| `country` | `VARCHAR(255)` | Nama negara |
| `country_code` | `VARCHAR(10)` | Kode negara (ISO 3166-1 alpha-2) |
| `timezone` | `VARCHAR(100)` | Zona waktu (e.g., `Asia/Jakarta`) |
| `created_at` | `TIMESTAMP` | Timestamp dibuat |
| `updated_at` | `TIMESTAMP` | Timestamp diperbarui |

> [!NOTE]
> Constraint duplikasi: kombinasi `(user_id, city_name, latitude, longitude)` harus unik. Implementasi via unique constraint di migration atau pengecekan manual di service sebelum insert.

---

## 8. Spesifikasi API Endpoint

**Base URL:** `https://[nama-app].onrender.com/api/v1`

### Ringkasan Endpoint

| # | Method | URL | Auth | Deskripsi |
|---|--------|-----|------|-----------|
| 1 | POST | `/auth/register` | ❌ | Registrasi akun baru |
| 2 | POST | `/auth/login` | ❌ | Login & dapat Bearer token |
| 3 | POST | `/auth/logout` | ✅ | Invalidasi token aktif |
| 4 | GET | `/weather/current` | ✅ | Data cuaca terkini |
| 5 | GET | `/weather/forecast` | ✅ | Prakiraan 7 hari |
| 6 | GET | `/geocoding/search` | ✅ | Cari kota (proxy Open-Meteo Geocoding) |
| 7 | GET | `/favorites` | ✅ | Daftar lokasi favorit user |
| 8 | POST | `/favorites` | ✅ | Tambah lokasi favorit |
| 9 | DELETE | `/favorites/{id}` | ✅ | Hapus lokasi favorit |
| 10 | GET | `/geocoding/boundary` | ✅ | GeoJSON batas wilayah kota |

---

### Endpoint 1 — Register Pengguna

**`POST /api/v1/auth/register`**

**Deskripsi:** Membuat akun pengguna baru. Password di-hash menggunakan bcrypt. Setelah register berhasil, mengembalikan data user dan Bearer token Sanctum langsung (sehingga FE tidak perlu login ulang).

**Request Body:**
```json
{
  "name": "string (required)",
  "email": "string (required, email format, unique)",
  "password": "string (required, min:8)",
  "password_confirmation": "string (required, same as password)"
}
```

**Response `201 Created`:**
```json
{
  "status": "success",
  "message": "Registrasi berhasil.",
  "data": {
    "user": { "id": 1, "name": "Naufal Tiftazani", "email": "naufal@student.ac.id", "created_at": "2025-01-01T10:00:00.000000Z" },
    "access_token": "1|LaravelSanctumTokenExample...",
    "token_type": "Bearer"
  }
}
```

**Response `422 Unprocessable Entity`:**
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

**Implementasi:**
- Form Request: `RegisterRequest.php` — validasi `name`, `email` (unique:users), `password` (min:8, confirmed)
- Controller: `AuthController@register`
- Hash password: `Hash::make($request->password)` sebelum create user
- Token: `$user->createToken('auth_token')->plainTextToken`

---

### Endpoint 2 — Login Pengguna

**`POST /api/v1/auth/login`**

**Deskripsi:** Autentikasi pengguna dan mengembalikan Bearer token Sanctum.

**Request Body:**
```json
{
  "email": "user@student.ac.id",
  "password": "password123"
}
```

**Response `200 OK`:**
```json
{
  "status": "success",
  "message": "Login berhasil.",
  "data": {
    "user": { "id": 1, "name": "Naufal Tiftazani", "email": "user@student.ac.id" },
    "access_token": "1|LaravelSanctumTokenExample...",
    "token_type": "Bearer"
  }
}
```

**Response `401 Unauthorized` (kredensial salah):**
```json
{
  "status": "error",
  "message": "Email atau password salah."
}
```

**Response `422 Unprocessable Entity` (validasi gagal):**
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

**Implementasi:**
- Form Request: `LoginRequest.php` — validasi `email` (required, email), `password` (required)
- Controller: `AuthController@login`
- Pengecekan: `Auth::attempt()` atau `Hash::check()` manual
- Jika gagal: return 401 dengan message error

---

### Endpoint 3 — Logout Pengguna

**`POST /api/v1/auth/logout`** — 🔒 Requires Bearer Token

**Deskripsi:** Menginvalidasi token Sanctum aktif pengguna (menghapus dari tabel `personal_access_tokens`).

**Request Headers:** `Authorization: Bearer <token>`

**Response `200 OK`:**
```json
{
  "status": "success",
  "message": "Logout berhasil. Token telah dihapus."
}
```

**Response `401 Unauthorized`:**
```json
{
  "status": "error",
  "message": "Token tidak valid atau sudah kedaluwarsa."
}
```

**Implementasi:**
- Controller: `AuthController@logout`
- `$request->user()->currentAccessToken()->delete()`

---

### Endpoint 4 — Get Current Weather

**`GET /api/v1/weather/current?latitude=&longitude=&city_name=`** — 🔒 Requires Bearer Token

**Deskripsi:** Proxy ke Open-Meteo API untuk mengambil data cuaca terkini. Backend memformat ulang respons sebelum dikembalikan ke FE.

**Query Parameters:**

| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|-----------|
| `latitude` | float | ✅ | Koordinat lintang |
| `longitude` | float | ✅ | Koordinat bujur |
| `city_name` | string | ✅ | Nama kota (untuk ditampilkan di response) |

**Open-Meteo Endpoint yang Dipanggil:**
```
GET https://api.open-meteo.com/v1/forecast
  ?latitude={lat}
  &longitude={lng}
  &current=temperature_2m,apparent_temperature,relative_humidity_2m,
           wind_speed_10m,wind_direction_10m,weather_code,is_day
  &wind_speed_unit=kmh
  &timezone=auto
```

**Response `200 OK`:**
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

**Response `400 Bad Request`:** Parameter `latitude`/`longitude` tidak diisi.

**Response `502 Bad Gateway`:** Open-Meteo tidak merespons.

**Implementasi:**
- Form Request: `GetWeatherRequest.php` — validasi `latitude` (required, numeric), `longitude` (required, numeric), `city_name` (required, string)
- Service: `OpenMeteoService@getCurrentWeather(float $lat, float $lng, string $cityName): array`
- Mapping `weather_code` ke kondisi bahasa Indonesia (e.g., `0` → `"Cerah"`, `61,63,65` → `"Hujan"`)
- Cache Key: `weather_current_{$lat}_{$lng}` — TTL: **10 menit** (data terkini tidak perlu cache panjang)

**Tabel Mapping Weather Code (WMO):**

| Code | Kondisi Indonesia |
|------|------------------|
| 0 | Cerah |
| 1, 2, 3 | Cerah Berawan |
| 45, 48 | Berkabut |
| 51, 53, 55 | Gerimis |
| 61, 63, 65 | Hujan |
| 71, 73, 75 | Hujan Salju |
| 80, 81, 82 | Hujan Lebat |
| 95 | Badai Petir |
| 96, 99 | Badai Petir dengan Hujan Es |

---

### Endpoint 5 — Get 7-Day Weather Forecast

**`GET /api/v1/weather/forecast?latitude=&longitude=&city_name=`** — 🔒 Requires Bearer Token

**Deskripsi:** Proxy ke Open-Meteo API untuk prakiraan 7 hari. Wajib di-cache untuk mengurangi beban API.

**Open-Meteo Endpoint yang Dipanggil:**
```
GET https://api.open-meteo.com/v1/forecast
  ?latitude={lat}
  &longitude={lng}
  &daily=temperature_2m_max,temperature_2m_min,weather_code,
         precipitation_sum,precipitation_probability_max,
         wind_speed_10m_max,relative_humidity_2m_mean
  &wind_speed_unit=kmh
  &timezone=auto
  &forecast_days=7
```

**Response `200 OK`:**
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

**Implementasi:**
- Service: `OpenMeteoService@getForecast(float $lat, float $lng, string $cityName): array`
- Cache Key: `weather_forecast_{$lat}_{$lng}` — **TTL: 1 jam (3600 detik)**
- Field `cached` dan `cache_expires_at` diisi berdasarkan status cache saat itu
- `day_name` diformat ke bahasa Indonesia menggunakan Carbon: `Carbon::parse($date)->translatedFormat('l')`

---

### Endpoint 6 — Search City (Geocoding)

**`GET /api/v1/geocoding/search?q=&count=5`** — 🔒 Requires Bearer Token

**Deskripsi:** Proxy ke Open-Meteo Geocoding API untuk mencari kota berdasarkan nama. Dipanggil saat pengguna mengetik di search bar.

> [!NOTE]
> **Catatan Khusus:** Berdasarkan arsitektur proyek, endpoint ini tetap disediakan oleh BE sebagai proxy. FE hanya boleh bypass langsung ke Open-Meteo Geocoding jika ada keputusan teknis dari tim.

**Open-Meteo Geocoding Endpoint yang Dipanggil:**
```
GET https://geocoding-api.open-meteo.com/v1/search?name={q}&count={count}&language=id&format=json
```

**Query Parameters:**

| Parameter | Tipe | Wajib | Default | Keterangan |
|-----------|------|-------|---------|-----------|
| `q` | string | ✅ | — | Nama kota (min. 2 karakter) |
| `count` | integer | ❌ | 5 | Jumlah hasil maksimal |

**Response `200 OK`:**
```json
{
  "status": "success",
  "data": {
    "query": "Banda Aceh",
    "count": 1,
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

**Response `422 Unprocessable Entity`:**
```json
{
  "status": "error",
  "message": "Parameter pencarian minimal 2 karakter."
}
```

**Implementasi:**
- Form Request: `SearchCityRequest.php` — validasi `q` (required, string, min:2), `count` (nullable, integer, min:1, max:10)
- Service: `OpenMeteoService@searchCity(string $query, int $count = 5): array`
- **Tidak perlu cache** (hasil search dinamis per query pengguna)

---

### Endpoint 7 — Get All Favorite Locations

**`GET /api/v1/favorites`** — 🔒 Requires Bearer Token

**Deskripsi:** Mengambil seluruh daftar lokasi favorit milik pengguna yang sedang login.

**Response `200 OK`:**
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
      }
    ]
  }
}
```

**Implementasi:**
- Controller: `FavoriteLocationController@index`
- Service: `FavoriteLocationService@getAll(int $userId): Collection`
- Query: `FavoriteLocation::where('user_id', $user->id)->orderBy('created_at', 'desc')->get()`

---

### Endpoint 8 — Add Favorite Location

**`POST /api/v1/favorites`** — 🔒 Requires Bearer Token

**Deskripsi:** Menyimpan lokasi baru ke daftar favorit. Mencegah duplikasi lokasi yang sama untuk satu pengguna.

**Request Body:**
```json
{
  "city_name": "string (required)",
  "latitude": "float (required)",
  "longitude": "float (required)",
  "country": "string (required)",
  "country_code": "string (required, max:10)",
  "timezone": "string (required)"
}
```

**Response `201 Created`:**
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

**Response `409 Conflict`** (lokasi sudah ada):
```json
{
  "status": "error",
  "message": "Lokasi ini sudah ada di daftar favorit Anda."
}
```

**Response `422 Unprocessable Entity`:**
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

**Implementasi:**
- Form Request: `StoreFavoriteRequest.php`
- Service: `FavoriteLocationService@store(int $userId, array $data): FavoriteLocation`
- Cek duplikasi: `FavoriteLocation::where(['user_id' => $userId, 'city_name' => $cityName])->exists()` → return 409 jika true

---

### Endpoint 9 — Delete Favorite Location

**`DELETE /api/v1/favorites/{id}`** — 🔒 Requires Bearer Token

**Deskripsi:** Menghapus lokasi dari daftar favorit. Hanya pemilik data yang dapat menghapus (ownership check wajib).

**Response `200 OK`:**
```json
{
  "status": "success",
  "message": "Lokasi berhasil dihapus dari favorit."
}
```

**Response `404 Not Found`:**
```json
{
  "status": "error",
  "message": "Lokasi favorit tidak ditemukan."
}
```

**Response `403 Forbidden`:**
```json
{
  "status": "error",
  "message": "Anda tidak memiliki izin untuk menghapus data ini."
}
```

**Implementasi:**
- Controller: `FavoriteLocationController@destroy`
- Service: `FavoriteLocationService@delete(int $userId, int $locationId): bool`
- Cari record: `FavoriteLocation::find($id)` → 404 jika null
- Ownership check: `$location->user_id !== $user->id` → 403 jika tidak sama
- Delete: `$location->delete()`

---

### Endpoint 10 — Get City Boundary GeoJSON

**`GET /api/v1/geocoding/boundary?q=`** — 🔒 Requires Bearer Token

**Deskripsi:** Proxy ke Nominatim (OpenStreetMap) untuk mengambil GeoJSON polygon batas wilayah kota. Wajib di-cache 24 jam. Digunakan FE untuk render outline wilayah di peta Mapbox GL.

**Nominatim Endpoint yang Dipanggil:**
```
GET https://nominatim.openstreetmap.org/search
  ?q={q}
  &polygon_geojson=1
  &format=json
  &limit=1
Headers:
  User-Agent: AeroCast/1.0 (contact: {email_kelompok})
  Accept-Language: id,en;q=0.9
```

> [!CAUTION]
> Wajib menyertakan header `User-Agent` saat request ke Nominatim. Tanpa ini, request bisa di-block. Gunakan format: `AeroCast/1.0 (contact: opensky@student.ac.id)`

**Query Parameters:**

| Parameter | Tipe | Wajib | Keterangan |
|-----------|------|-------|-----------|
| `q` | string | ✅ | Nama kota/wilayah (min. 2 karakter) |

**Response `200 OK`:**
```json
{
  "status": "success",
  "data": {
    "query": "Banda Aceh",
    "cached": true,
    "cache_expires_at": "2025-01-02T10:00:00Z",
    "boundary": {
      "type": "Feature",
      "properties": {
        "display_name": "Banda Aceh, Aceh, Indonesia",
        "osm_id": 3629770,
        "place_id": 298765432,
        "boundingbox": ["5.4921", "5.6083", "95.2615", "95.4065"]
      },
      "geometry": {
        "type": "Polygon",
        "coordinates": [[[95.2615, 5.4921], [95.4065, 5.4921], ["..."]]]
      }
    }
  }
}
```

**Response `404 Not Found`:** Kota tidak ditemukan di Nominatim.

**Response `422 Unprocessable Entity`:** Query terlalu pendek (< 2 karakter).

**Response `502 Bad Gateway`:** Nominatim tidak merespons.

**Implementasi:**
- Form Request: `GetBoundaryRequest.php` — validasi `q` (required, string, min:2)
- Service: `NominatimService@getBoundary(string $query): array`
- Cache Key: `boundary_{$queryNormalized}` — format: `strtolower(str_replace(' ', '_', $query))`
- **TTL: 24 jam (86400 detik)**
- Jika `geometry.type === "MultiPolygon"`: kembalikan as-is (Mapbox GL mendukung keduanya)

---

## 9. Format Response Standar

**Semua** response dari backend wajib menggunakan format berikut:

```php
// Sukses dengan data:
return response()->json([
    'status'  => 'success',
    'message' => 'Data berhasil diambil.',
    'data'    => $data,
], 200);

// Sukses tanpa data (misal: logout, delete):
return response()->json([
    'status'  => 'success',
    'message' => 'Operasi berhasil.',
], 200);

// Error validasi:
return response()->json([
    'status'  => 'error',
    'message' => 'Validasi gagal.',
    'errors'  => $validationErrors,
], 422);

// Error umum:
return response()->json([
    'status'  => 'error',
    'message' => 'Deskripsi error.',
], $httpStatusCode);
```

---

## 10. Strategi Caching

| Data | Cache Key | TTL | Driver |
|------|-----------|-----|--------|
| Cuaca terkini | `weather_current_{lat}_{lng}` | 10 menit | Redis/File |
| Forecast 7 hari | `weather_forecast_{lat}_{lng}` | 1 jam | Redis/File |
| GeoJSON boundary | `boundary_{q_normalized}` | 24 jam | Redis/File |
| Hasil geocoding search | — | Tidak di-cache | — |

**Pola implementasi cache:**

```php
$data = Cache::remember($cacheKey, $ttl, function () use ($lat, $lng) {
    return $this->callOpenMeteoApi($lat, $lng);
});
```

---

## 11. Penanganan Error & HTTP Status Code

| Skenario | Status Code |
|----------|-------------|
| Request berhasil | `200 OK` |
| Resource baru berhasil dibuat | `201 Created` |
| Validasi input gagal | `422 Unprocessable Entity` |
| Token tidak ada / tidak valid | `401 Unauthorized` |
| Tidak memiliki izin akses | `403 Forbidden` |
| Resource tidak ditemukan | `404 Not Found` |
| Data sudah ada (duplikat) | `409 Conflict` |
| Third-party API gagal/timeout | `502 Bad Gateway` |
| Server error internal | `500 Internal Server Error` |

**Global Exception Handler (`app/Exceptions/Handler.php`)** harus menangkap:
- `AuthenticationException` → 401 dengan format standar
- `AuthorizationException` → 403 dengan format standar
- `ModelNotFoundException` → 404 dengan format standar
- `ValidationException` → 422 dengan format standar + field `errors`
- `ConnectionException` (Guzzle/HTTP Client) → 502 dengan pesan user-friendly
- Exception lain → 500 dengan pesan generik (sembunyikan detail error di production)

---

## 12. Routing (`routes/api.php`)

```php
Route::prefix('v1')->group(function () {

    // Auth — public
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login',    [AuthController::class, 'login']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {

        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::prefix('weather')->group(function () {
            Route::get('/current',  [WeatherController::class, 'current']);
            Route::get('/forecast', [WeatherController::class, 'forecast']);
        });

        Route::prefix('geocoding')->group(function () {
            Route::get('/search',   [GeocodingController::class, 'search']);
            Route::get('/boundary', [GeocodingController::class, 'boundary']);
        });

        Route::apiResource('favorites', FavoriteLocationController::class)
            ->only(['index', 'store', 'destroy']);
    });
});
```

---

## 13. Konvensi Kode Backend

Mengacu pada [Codingconvention.md](file:///d:/SWDM/Project/Codingconvention.md):

| Konteks | Convention | Contoh |
|---------|-----------|--------|
| Controller | `PascalCase` + `Controller` | `WeatherController.php` |
| Service | `PascalCase` + `Service` | `OpenMeteoService.php` |
| Model | `PascalCase`, singular | `FavoriteLocation.php` |
| Migration | `snake_case`, `verb_noun_table` | `create_favorite_locations_table.php` |
| Form Request | `PascalCase`, `ActionNounRequest` | `StoreFavoriteRequest.php` |
| Method | `camelCase` | `getCurrentWeather()` |
| Variabel lokal | `camelCase` | `$weatherData`, `$cacheKey` |
| Konstanta class | `SCREAMING_SNAKE_CASE` | `CACHE_TTL_FORECAST = 3600` |
| Tabel DB | `snake_case`, plural | `favorite_locations` |
| Kolom DB | `snake_case` | `city_name`, `country_code` |

---

## 14. Variabel Environment (`.env`)

```env
# App
APP_NAME=AeroCast
APP_ENV=production
APP_DEBUG=false
APP_URL=https://[nama-app].onrender.com

# Database — Supabase PostgreSQL
DB_CONNECTION=pgsql
DB_HOST=db.[project-ref].supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=[supabase-db-password]

# Cache
CACHE_DRIVER=redis   # atau: file (jika Redis tidak tersedia di Render free tier)
REDIS_URL=[redis-url]

# Sanctum
SANCTUM_STATEFUL_DOMAINS=[frontend-domain].onrender.com

# Third-party API — tidak memerlukan API key
OPEN_METEO_BASE_URL=https://api.open-meteo.com/v1
OPEN_METEO_GEOCODING_URL=https://geocoding-api.open-meteo.com/v1
NOMINATIM_BASE_URL=https://nominatim.openstreetmap.org
NOMINATIM_USER_AGENT="AeroCast/1.0 (contact: opensky@student.ac.id)"
```

> [!CAUTION]
> Jangan pernah commit file `.env` ke repository. Semua secret dimasukkan ke **GitHub Secrets** dan **Render Environment Variables**.

---

## 15. Kriteria Penerimaan (Acceptance Criteria)

### Fitur 1 — Autentikasi

- [ ] `POST /auth/register` membuat user baru dengan password ter-hash dan mengembalikan token
- [ ] `POST /auth/register` menolak email duplikat dengan error 422
- [ ] `POST /auth/login` mengembalikan Bearer token jika kredensial valid
- [ ] `POST /auth/login` mengembalikan 401 jika email/password salah
- [ ] `POST /auth/logout` menghapus token dari database dan user tidak bisa menggunakannya lagi
- [ ] Semua endpoint protected mengembalikan 401 jika token tidak disertakan

### Fitur 3 — Forecast

- [ ] `GET /weather/current` mengembalikan data cuaca terkini dengan field yang lengkap
- [ ] `GET /weather/forecast` mengembalikan prakiraan 7 hari dengan array `forecast`
- [ ] Response forecast menyertakan `cached: true/false` dan `cache_expires_at`
- [ ] Cache forecast berlaku 1 jam; request kedua dalam 1 jam tidak memanggil Open-Meteo

### Fitur 5 — Favorit

- [ ] `GET /favorites` hanya mengembalikan data milik user yang sedang login
- [ ] `POST /favorites` berhasil menyimpan lokasi baru
- [ ] `POST /favorites` mengembalikan 409 jika lokasi sudah ada di favorit user
- [ ] `DELETE /favorites/{id}` berhasil menghapus data milik sendiri
- [ ] `DELETE /favorites/{id}` mengembalikan 403 jika mencoba menghapus data milik user lain

### Fitur 7 — GeoJSON Boundary

- [ ] `GET /geocoding/boundary` mengembalikan GeoJSON valid dari Nominatim
- [ ] Cache boundary berlaku 24 jam
- [ ] Header `User-Agent` selalu disertakan saat request ke Nominatim
- [ ] Mendukung geometry type `Polygon` dan `MultiPolygon`

---

## 16. Rencana Verifikasi

### Testing Manual (via Postman/Insomnia)

1. Register akun baru → verifikasi token dikembalikan
2. Login dengan akun baru → verifikasi token
3. Gunakan token untuk akses `/weather/current?latitude=-6.2088&longitude=106.8456&city_name=Jakarta`
4. Akses `/weather/forecast` dengan koordinat yang sama — cek respons pertama (`cached: false`) dan respons kedua dalam 1 jam (`cached: true`)
5. Tambah 2 lokasi favorit → verifikasi tidak ada duplikat
6. Coba hapus favorit milik user lain → verifikasi 403
7. Akses `/geocoding/boundary?q=Jakarta` → verifikasi GeoJSON valid
8. Logout → coba gunakan token lama → verifikasi 401

### Validasi Struktur Response

Semua response wajib memiliki field `status` dan `message`. Response sukses dengan data wajib memiliki field `data`.

---

## 17. Referensi

| Dokumen | Path |
|---------|------|
| AGENTS.md | [AGENTS.md](file:///d:/SWDM/Project/Agents.md) |
| Coding Convention | [Codingconvention.md](file:///d:/SWDM/Project/Codingconvention.md) |
| Identitas Kelompok | [01-identitas-kelompok.md](file:///d:/SWDM/Project/Instruksi-Tugas/01-identitas-kelompok.md) |
| Rencana Fitur | [02-rencana-fitur.md](file:///d:/SWDM/Project/Instruksi-Tugas/02-rencana-fitur.md) |
| API Spec | [03-api-spec.md](file:///d:/SWDM/Project/Instruksi-Tugas/03-api-spec.md) |
| Open-Meteo Docs | https://open-meteo.com/en/docs |
| Open-Meteo Geocoding | https://open-meteo.com/en/docs/geocoding-api |
| Nominatim API | https://nominatim.org/release-docs/develop/api/Search/ |
| Laravel Sanctum | https://laravel.com/docs/sanctum |
| Laravel HTTP Client | https://laravel.com/docs/http-client |
| Laravel Cache | https://laravel.com/docs/cache |

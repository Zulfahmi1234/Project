# Handover Document: Backend ke Frontend (AeroCast)

Dokumen ini berisi panduan dan bahan yang diperlukan oleh **Frontend Developer (Ikhlassul)** untuk mulai membangun aplikasi AeroCast menggunakan Next.js.

## 1. Status Backend
Backend (Laravel 13) saat ini **SUDAH SELESAI 100%** dan berjalan di lokal. Backend bertindak sebagai API Gateway yang menangani Autentikasi (Sanctum), Database (PostgreSQL via Supabase), serta mem-proxy request ke Open-Meteo dan Nominatim.

## 2. Environment Variables yang Dibutuhkan Frontend
Buat file `.env.local` di folder root project Next.js nantinya, dan isi dengan variabel berikut:

```env
# URL Backend Laravel (Pastikan php artisan serve jalan di port 8000)
NEXT_PUBLIC_API_BASE_URL=http://localhost:8000/api/v1

# Token Mapbox (Dapatkan dari https://account.mapbox.com)
NEXT_PUBLIC_MAPBOX_TOKEN=masukkan_token_mapbox_anda_disini
```

## 3. Cara Menguji API (Postman)
Anda tidak perlu menebak-nebak format API. Silakan gunakan Postman Collection yang sudah kami siapkan:
- File: `AeroCast_Postman_Collection.json` (ada di folder utama project ini).
- Cara pakai: Buka aplikasi Postman -> Klik **Import** -> Pilih file tersebut.
- *Catatan:* Collection ini sudah dilengkapi *script otomatis*! Setelah Anda hit endpoint `Login`, token akan otomatis tersimpan dan bisa langsung dipakai untuk endpoint lain tanpa perlu di-copy-paste manual.

## 4. Daftar API Endpoints yang Tersedia
Semua endpoint berawalan `http://localhost:8000/api/v1`. Format kembalian (response) selalu seragam: `{ "status": "...", "message": "...", "data": {...} }`.

| Method | Endpoint | Fitur | Perlu Token (Login)? |
| :--- | :--- | :--- | :---: |
| POST | `/auth/register` | Membuat akun baru | ❌ |
| POST | `/auth/login` | Mendapatkan Bearer Token | ❌ |
| POST | `/auth/logout` | Menghapus session | ✅ |
| GET | `/weather/current?latitude=&longitude=&city_name=` | Cuaca saat ini (Floating panel atas) | ✅ |
| GET | `/weather/forecast?latitude=&longitude=&city_name=` | Prakiraan 7 hari (Floating panel bawah) | ✅ |
| GET | `/geocoding/search?q=&count=5` | Cari kota di Search Bar | ✅ |
| GET | `/geocoding/boundary?q=` | Ambil GeoJSON Polygon wilayah kota | ✅ |
| GET | `/favorites` | Ambil daftar lokasi favorit | ✅ |
| POST | `/favorites` | Simpan kota ke favorit | ✅ |
| DELETE | `/favorites/{id}` | Hapus kota dari favorit | ✅ |

*(Detail struktur *request/response* lengkap ada di file `Instruksi-Tugas/03-api-spec.md`)*

## 5. Panduan & Aturan Ketat untuk Frontend
Sesuai dokumen `AGENTS.md` dan `Codingconvention.md`, berikut adalah hal wajib untuk Frontend:
1. **Dilarang Tembak API Eksternal Secara Langsung:** Frontend **tidak boleh** memanggil `api.open-meteo.com` atau `nominatim.openstreetmap.org` secara langsung. Semua harus lewat Backend `localhost:8000` (kecuali geocoding *search* jika butuh sangat *real-time*, tapi saat ini backend sudah sanggup melayani via proxy).
2. **Setup Axios Interceptor:** Buat file `lib/axios.ts`. Atur agar Axios otomatis menyisipkan header `Authorization: Bearer <token dari localStorage/cookie>`. **Jangan pakai `fetch` bawaan JS.**
3. **React Query:** Semua pemanggilan API ke backend (GET/POST) harus menggunakan *TanStack React Query* agar data ter-cache dengan rapi di state frontend.
4. **Cinematic Zoom:** Saat user mencari kota atau mengklik daftar favorit, gunakan perintah `map.flyTo({ center, zoom: 11, duration: 1800 })` milik Mapbox GL JS sebelum memunculkan *Floating Weather Panel*.
5. **UI/UX:** Wajib menggunakan *Tailwind CSS v4*, komponen dari *shadcn/ui*, animasi dari *framer-motion* atau *auto-animate*, dan *recharts* untuk grafik suhu. Jangan lupa *Glassmorphism* untuk panel!

---
*Semangat mengerjakan bagian UI! Jika ada API yang bermasalah, segera hubungi Backend Developer (Naufal).*

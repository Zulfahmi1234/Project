<?php

namespace App\Contracts;

use App\Models\FavoriteLocation;
use Illuminate\Database\Eloquent\Collection;

/**
 * Kontrak untuk manajemen lokasi favorit.
 *
 * Menangani operasi CRUD untuk lokasi yang disimpan (bookmark) oleh pengguna.
 * Semua operasi dibatasi (scoped) pada ID pengguna yang terautentikasi
 * untuk menegakkan hak kepemilikan.
 */
interface FavoriteLocationServiceInterface
{
    /**
     * Mengambil semua lokasi favorit milik seorang pengguna.
     *
     * Hasil diurutkan berdasarkan tanggal pembuatan (terbaru di atas).
     *
     * @param int $userId ID pengguna yang terautentikasi
     * @return Collection<int, FavoriteLocation>
     */
    public function getAll(int $userId): Collection;

    /**
     * Menyimpan lokasi favorit baru.
     *
     * Melakukan pengecekan duplikasi berdasarkan (user_id, city_name, latitude, longitude).
     * Jika duplikat ditemukan, maka akan mengembalikan flag duplicate tanpa membuat record baru.
     *
     * @param int   $userId ID pengguna yang terautentikasi
     * @param array{
     *     city_name: string,
     *     latitude: float,
     *     longitude: float,
     *     country: string,
     *     country_code: string,
     *     timezone: string
     * } $data Data lokasi yang sudah divalidasi
     * @return array{location: FavoriteLocation|null, duplicate: bool}
     */
    public function store(int $userId, array $data): array;

    /**
     * Menghapus lokasi favorit berdasarkan ID.
     *
     * Memverifikasi kepemilikan sebelum penghapusan. Mengembalikan hasil dengan
     * flag success dan alasan kegagalan jika ada.
     *
     * @param int $userId     ID pengguna yang terautentikasi
     * @param int $locationId ID lokasi favorit yang akan dihapus
     * @return array{success: bool, reason: string|null}
     *     reason bisa bernilai: 'not_found' | 'forbidden' | null (jika sukses)
     */
    public function delete(int $userId, int $locationId): array;
}

<?php

namespace App\Contracts;

use App\Models\User;

/**
 * Kontrak untuk operasi autentikasi.
 *
 * Menangani registrasi pengguna, verifikasi kredensial login,
 * dan manajemen token (berbasis Sanctum).
 */
interface AuthServiceInterface
{
    /**
     * Mendaftarkan pengguna baru dan membuat access token.
     *
     * @param array{name: string, email: string, password: string} $data Data registrasi yang sudah divalidasi
     * @return array{user: User, access_token: string, token_type: string}
     */
    public function register(array $data): array;

    /**
     * Mencoba melakukan autentikasi pengguna dengan email dan password.
     *
     * Mengembalikan data pengguna dan access token jika sukses, atau null jika gagal.
     *
     * @param array{email: string, password: string} $credentials Kredensial login yang sudah divalidasi
     * @return array{user: User, access_token: string, token_type: string}|null
     */
    public function login(array $credentials): ?array;

    /**
     * Mengeluarkan (logout) pengguna yang terautentikasi dengan mencabut access token saat ini.
     *
     * @param User $user Pengguna yang terautentikasi
     * @return void
     */
    public function logout(User $user): void;
}

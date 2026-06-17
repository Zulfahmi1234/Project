<?php

namespace App\Contracts;

use App\Models\User;

/**
 * Contract for authentication operations.
 *
 * Handles user registration, login credential verification,
 * and token management (Sanctum-based).
 */
interface AuthServiceInterface
{
    /**
     * Register a new user and create an access token.
     *
     * @param array{name: string, email: string, password: string} $data Validated registration data
     * @return array{user: User, access_token: string, token_type: string}
     */
    public function register(array $data): array;

    /**
     * Attempt to authenticate a user with email and password.
     *
     * Returns user data and access token on success, or null on failure.
     *
     * @param array{email: string, password: string} $credentials Validated login credentials
     * @return array{user: User, access_token: string, token_type: string}|null
     */
    public function login(array $credentials): ?array;

    /**
     * Logout the authenticated user by revoking the current access token.
     *
     * @param User $user The authenticated user
     * @return void
     */
    public function logout(User $user): void;
}

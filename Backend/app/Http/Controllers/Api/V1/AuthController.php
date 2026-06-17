<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\AuthServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService,
    ) {}

    /**
     * Register a new user.
     *
     * POST /api/v1/auth/register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'status'  => 'success',
            'message' => 'Registrasi berhasil.',
            'data'    => [
                'user' => [
                    'id'         => $result['user']->id,
                    'name'       => $result['user']->name,
                    'email'      => $result['user']->email,
                    'created_at' => $result['user']->created_at,
                ],
                'access_token' => $result['access_token'],
                'token_type'   => $result['token_type'],
            ],
        ], 201);
    }

    /**
     * Login an existing user.
     *
     * POST /api/v1/auth/login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if ($result === null) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Email atau password salah.',
            ], 401);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Login berhasil.',
            'data'    => [
                'user' => [
                    'id'    => $result['user']->id,
                    'name'  => $result['user']->name,
                    'email' => $result['user']->email,
                ],
                'access_token' => $result['access_token'],
                'token_type'   => $result['token_type'],
            ],
        ], 200);
    }

    /**
     * Logout the authenticated user.
     *
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'status'  => 'success',
            'message' => 'Logout berhasil. Token telah dihapus.',
        ], 200);
    }
}


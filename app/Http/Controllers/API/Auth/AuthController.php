<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\Auth\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $this->authService->login($request->validated(), $request);

        return response()->json([
            'status' => true,
            'message' => 'Signed in successfully.',
            'data' => $data,
        ]);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated(), $request);

        return response()->json([
            'status' => true,
            'message' => 'Account created successfully.',
            'data' => $data,
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Authenticated user retrieved.',
            'data' => [
                'user' => $this->authService->me($request->user()),
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user(), $request);

        return response()->json([
            'status' => true,
            'message' => 'Signed out successfully.',
            'data' => [],
        ]);
    }

    public function logoutOtherDevices(Request $request): JsonResponse
    {
        $data = $this->authService->logoutOtherDevices($request->user(), $request);

        return response()->json([
            'status' => true,
            'message' => 'Other devices have been signed out.',
            'data' => $data,
        ]);
    }
}

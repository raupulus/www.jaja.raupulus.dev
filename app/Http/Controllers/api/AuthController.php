<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required', // Nombre del dispositivo para el token
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse(__('auth.failed'), 401);
        }

        return $this->successResponse([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => new UserResource($user)
        ], 'Login exitoso');

    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        // Para revocar todos los tokens del usuario: $request->user()->tokens()->delete();

        return $this->successResponse(null, 'Sesión cerrada exitosamente');
    }

    public function user(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()),
            'Información del usuario obtenida'
        );

    }
}

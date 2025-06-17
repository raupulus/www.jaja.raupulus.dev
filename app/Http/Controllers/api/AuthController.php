<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserLoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Login de usuario para obtener token de acceso api y de sesión (para SPA por ejemplo)
     *
     * @param UserLoginRequest $request Request validado con email, password y device_name
     * @return JsonResponse
     *
     * @throws ValidationException Si los datos no son válidos
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->errorResponse(__('auth.failed'), 401);
        }

        return $this->successResponse([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => new UserResource($user)
        ], 'Login exitoso');
    }

    /**
     * Cierra la sesión de un usuario e invalida el token de acceso utilizado en ese momento
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Sesión cerrada exitosamente');
    }

    /**
     * Elimina todos los tokens válidos para el usuario
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteAllTokens(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return $this->successResponse(null, 'Sesión cerrada exitosamente');
    }

    /**
     * Devuelve la información del usuario actualmente logueado en la plataforma
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function user(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()),
            'Información del usuario obtenida'
        );

    }
}

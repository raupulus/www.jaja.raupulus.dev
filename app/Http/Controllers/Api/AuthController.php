<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserLoginRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Controlador AuthController
 *
 * Gestiona la autenticación de la API mediante Sanctum (login, logout, perfil y subida de contenido de usuario).
 */
class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * Login
     *
     * Limitado a 3 intentos por minuto
     *
     * Lo usamos para obtener token de acceso api y de sesión (para SPA por ejemplo)
     *
     * El Token Bearer devuelto lo usaremos en los headers para las peticiones que requieran autenticación:
     *
     * Bearer 5|dpsZX6OKLdrx1wYDfJqyMjg3kdAGdrmzDU4gMkJ1be4af09b
     *
     * @group 🔐 Autenticación
     * @unauthenticated
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "token": "5|dpsZX6OKLdrx1wYDfJqyMjg3kdAGdrmzDU4gMkJ1be4af09b",
     *     "user": {
     *       "name": "Juan Pérez",
     *       "nick": "juanito",
     *       "urlImage": "https://ejemplo.com/storage/user-images/avatar.webp",
     *       "email": "juan@ejemplo.com",
     *       "email_verified_at": "2024-01-15T10:30:00.000000Z"
     *     }
     *   },
     *   "message": "Login exitoso"
     * }
     *
     * @response 401 {
     *   "success": false,
     *   "message": "Las credenciales proporcionadas son incorrectas."
     * }
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data object Datos de respuesta del login (solo si es exitoso)
     * @responseField data.token string Token Bearer para autenticación en futuras peticiones
     * @responseField data.user object Información del usuario autenticado
     * @responseField data.user.name string Nombre completo del usuario
     * @responseField data.user.nick string Apodo único del usuario
     * @responseField data.user.urlImage string URL completa de la imagen de perfil
     * @responseField data.user.email string Email del usuario (incluido al ser el propio usuario)
     * @responseField data.user.email_verified_at string Fecha de verificación del email ISO 8601
     *
     * @param UserLoginRequest $request Request validado con email, password y device_name
     * @return JsonResponse
     * @throws ValidationException Si los datos no son válidos
     */
    public function login(UserLoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse(__('auth.failed'), 401);
        }

        return $this->successResponse([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => new UserResource($user)
        ], 'Login exitoso');
    }

    /**
     * Logout
     *
     * Cierra la sesión de un usuario e invalida el token de acceso utilizado en ese momento
     *
     * @group 🔐 Autenticación
     *
     * @response 200 {
     *   "success": true,
     *   "data": null,
     *   "message": "Sesión cerrada exitosamente"
     * }
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data null No se devuelven datos adicionales
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
     * Cerrar todas las sesiones
     *
     * Elimina todos los tokens válidos para el usuario
     *
     * @group 🔐 Autenticación
     *
     * @response 200 {
     *   "success": true,
     *   "data": null,
     *   "message": "Sesión cerrada exitosamente"
     * }
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data null No se devuelven datos adicionales
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
     * Información de usuario
     *
     * Devuelve la información del usuario actualmente logueado en la plataforma
     *
     * @group 🔐 Autenticación
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "name": "Juan Pérez",
     *     "nick": "juanito",
     *     "urlImage": "https://ejemplo.com/storage/user-images/avatar.webp",
     *     "email": "juan@ejemplo.com",
     *     "email_verified_at": "2024-01-15T10:30:00.000000Z"
     *   },
     *   "message": "Información del usuario obtenida"
     * }
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data object Datos del usuario
     * @responseField data.name string Nombre completo del usuario
     * @responseField data.nick string Apodo único del usuario (máximo 25 caracteres)
     * @responseField data.urlImage string URL completa de la imagen de perfil del usuario
     * @responseField data.email string Email del usuario (solo si es el propio usuario autenticado)
     * @responseField data.email_verified_at string Fecha de verificación del email en formato ISO 8601 (solo si es el propio usuario autenticado)
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

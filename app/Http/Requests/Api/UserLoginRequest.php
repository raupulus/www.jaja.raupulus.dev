<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Request para el login de usuario
 *
 * @property string $email Email del usuario
 * @property string $password Contraseña del usuario
 * @property string $device_name Nombre del dispositivo para generar el token y asociarlo a este
 */

class UserLoginRequest extends FormRequest
{
    use ApiResponseTrait;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => trim(strtolower($this->input('email'))),
            'password' => trim($this->input('password')),
            'device_name' => trim($this->input('device_name')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string|min:8|max:100',
            'device_name' => 'required|string|max:100', // Nombre del dispositivo para el token
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'El campo email es obligatorio.',
            'email.string' => 'El campo email debe ser una cadena de texto.',
            'email.email' => 'El campo email debe tener formato correcto de email',
            'password.required' => 'El campo password es obligatorio.',
            'password.string' => 'El campo password debe ser una cadena de texto.',
            'password.min' => 'El campo password debe tener al menos 8 caracteres.',
            'password.max' => 'El campo password debe tener máximo 100 caracteres.',
            'device_name.required' => 'El campo device_name es obligatorio.',
            'device_name.string' => 'El campo device_name debe ser una cadena de texto.',
            'device_name.max' => 'El campo device_name debe tener máximo 100 caracteres.',
        ];
    }

    /**
     * Handle a failed validation attempt for API.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->errorResponse('Errores de validación', 422, $validator->errors())
        );
    }

    /**
     * Parámetros del body para documentación API
     * Este método es usado por Scribe para generar la documentación
     */
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'Email del usuario',
                'example' => 'user@example.com',
            ],
            'password' => [
                'description' => 'Contraseña del usuario',
                'example' => 'mipasswordsupersegura123123123',
            ],
            'device_name' => [
                'description' => 'Nombre del dispositivo para generar el token y asociarlo a este',
                'example' => 'Mi iPhone 13 Pro',
            ]
        ];
    }

}

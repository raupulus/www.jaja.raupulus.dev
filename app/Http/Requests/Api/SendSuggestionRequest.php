<?php

namespace App\Http\Requests\Api;

use App\Models\Type;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SendSuggestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Obtiene la IP real del usuario considerando Cloudflare
     */
    public function getRealIpAddress(): string
    {
        // Headers que Cloudflare puede usar para enviar la IP real
        $headers = [
            'CF-Connecting-IP',      // Header principal de Cloudflare
            'HTTP_CF_CONNECTING_IP', // Variante del header de Cloudflare
            'X-Forwarded-For',       // Header estándar para proxies
            'X-Real-IP',             // Header alternativo
            'HTTP_X_FORWARDED_FOR',  // Variante del X-Forwarded-For
            'HTTP_X_REAL_IP',        // Variante del X-Real-IP
        ];

        foreach ($headers as $header) {
            $ip = $this->header($header) ?? $_SERVER[$header] ?? null;

            if ($ip) {
                // Si hay múltiples IPs separadas por comas, toma la primera
                $ip = trim(explode(',', $ip)[0]);

                // Valida que sea una IP válida
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        // Si no se encuentra ninguna IP válida, usa la IP por defecto
        return $this->ip();
    }

    public function prepareForValidation()
    {
        $nick = Str::replace(' ', '_', trim($this->nick ?? ''));

        if (Str::startsWith($nick, '@')) {
            $nick = Str::substr($nick, 1);
        }

        $nick = preg_replace('/[^a-zA-Z0-9_]/', '', $nick);
        $type = Type::where('slug', 'chistes')->first();

        $this->merge([
            'type_id' => $type->id,
            'nick' => $nick,
            'title' => trim($this->title) ?? 'Sugerencia',
            'ip_address' => $this->getRealIpAddress(),
            'user_agent' => $this->userAgent(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //'type_id' => 'required|exists:types,id',
            'nick' => 'sometimes|string|max:25|regex:/^[a-zA-Z0-9_]+$/',
            'title' => 'sometimes|string|max:255',
            'content' => 'required|string|max:1024',
            //'ip_address' => 'nullable|ip',
            //'user_agent' => 'nullable|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'type_id.required' => 'Debes seleccionar un tipo',
            'type_id.exists' => 'El tipo seleccionado no es válido',
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no debe superar los :max caracteres',
            'content.required' => 'El contenido es obligatorio',
            'content.max' => 'El contenido no debe superar los :max caracteres',
            'nick.max' => 'El nick no debe superar los :max caracteres',
            'nick.regex' => 'El nick solo puede contener letras, números y guiones bajos sin @',
            'nick.required' => 'El nick es obligatorio',
            'nick.string' => 'El nick debe ser una cadena de texto',
            'throttle' => 'Has enviado demasiadas sugerencias recientemente. Por favor, espera un momento antes de enviar otra.',
        ];
    }

    /**
     * Especifica los parámetros del cuerpo para la documentación de Scribe
     * Solo se mostrarán estos campos en la documentación de la API
     */
    public function bodyParameters(): array
    {
        return [
            'nick' => [
                'description' => 'Nombre de usuario o nick del autor de la sugerencia (máximo 25 caracteres, solo letras, números y guiones bajos)',
                'example' => 'mi_usuario_123',
                'required' => false,
            ],
            'title' => [
                'description' => 'Título de la sugerencia (máximo 255 caracteres)',
                'example' => 'Pájaros caminantes',
                'required' => false,
            ],
            'content' => [
                'description' => 'Contenido principal de la sugerencia (chiste, adivinanza, etc.) - máximo 1024 caracteres',
                'example' => '¿Por qué los pájaros vuelan hacia el sur? Porque caminando tardarían mucho.',
                'required' => true,
            ],
        ];
    }
}

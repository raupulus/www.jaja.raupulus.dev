<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class SuggestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $ip = $this->ip();
            $throttleKey = 'suggestions:' . $ip;

            // Si hay demasiados intentos, añado un error de validación
            if (!RateLimiter::attempt($throttleKey, 1, fn() => true, 10)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                $validator->errors()->add(
                    'throttle',
                    "Por favor, espera {$seconds} segundos antes de enviar otra sugerencia."
                );
            }
        });
    }

    protected function passedValidation()
    {
        $ip = $this->ip();
        $throttleKey = 'suggestions:' . $ip;
        RateLimiter::clear($throttleKey);

        if (! Auth::check()) {
            RateLimiter::hit($throttleKey, 60); // 60 segundos (1 minuto) de espera
        }

    }

    public function prepareForValidation()
    {
        $nick = Str::replace(' ', '_', trim($this->nick ?? ''));

        if (Str::startsWith($nick, '@')) {
            $nick = Str::substr($nick, 1);
        }

        $nick = preg_replace('/[^a-zA-Z0-9_]/', '', $nick);

        $this->merge([
            'nick' => $nick,
            'ip_address' => $this->ip(),
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
            'type_id' => 'required|exists:types,id',
            'nick' => 'nullable|string|max:255|regex:/^[a-zA-Z0-9_]+$/',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1024',
            'image' => 'nullable|image|max:2048',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string|max:255',
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
            'image.image' => 'El archivo debe ser una imagen, preferiblemente webp, jpg o png',
            'image.max' => 'La imagen no debe superar los :max kilobytes',
            'nick.max' => 'El nick no debe superar los :max caracteres',
            'nick.regex' => 'El nick solo puede contener letras, números y guiones bajos sin @',
            'throttle' => 'Has enviado demasiadas sugerencias recientemente. Por favor, espera un momento antes de enviar otra.',
        ];
    }
}

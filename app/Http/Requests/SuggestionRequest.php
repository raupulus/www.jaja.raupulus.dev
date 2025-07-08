<?php

namespace App\Http\Requests;

use App\Models\Type;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

/**
 * Request para envíos de sugerencia de contenido
 *
 * @param int $type_id Id del tipo de contenido asociado
 * @param string $nick Nombre del usuario en las redes sociales con formato de username o nick
 * @param string $title Título principal del contenido
 * @param string $content Contenido
 * @param array $options Opciones para quiz (solo requerido si type_id es quiz)
 */
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
     * Obtiene la IP real del usuario considerando Cloudflare
     */
    protected function getRealIpAddress(): string
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

    /**
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $ip = $this->getRealIpAddress();
            $throttleKey = 'suggestions:' . $ip;

            // Si hay demasiados intentos, añado un error de validación
            if (!RateLimiter::attempt($throttleKey, 1, fn() => true, 10)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                $validator->errors()->add(
                    'throttle',
                    "Por favor, espera {$seconds} segundos antes de enviar otra sugerencia."
                );
            }

            // Validación personalizada para quiz
            $this->validateQuizOptions($validator);
        });
    }

    /**
     * Validación específica para opciones de quiz
     */
    private function validateQuizOptions(Validator $validator): void
    {
        if (!$this->type_id) {
            return;
        }

        $type = Type::find($this->type_id);
        if (!$type || $type->slug !== 'quiz') {
            return;
        }

        $options = $this->input('options', []);

        // Verificar que hay al menos 2 opciones
        if (count($options) < 2) {
            $validator->errors()->add(
                'options',
                'Para preguntas tipo quiz debes añadir al menos 2 respuestas.'
            );
            return;
        }

        // Verificar que hay exactamente una respuesta correcta
        $correctAnswers = array_filter($options, fn($option) => $option['is_correct'] ?? false);
        if (count($correctAnswers) !== 1) {
            $validator->errors()->add(
                'options',
                'Para preguntas tipo quiz debe haber exactamente una respuesta correcta.'
            );
        }

        // Validar cada opción individualmente
        foreach ($options as $index => $option) {
            if (empty($option['value'])) {
                $validator->errors()->add(
                    "options.{$index}.value",
                    'El valor de la respuesta es obligatorio.'
                );
            }
        }
    }

    protected function passedValidation()
    {
        $ip = $this->getRealIpAddress();
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

        $options = [];

        if ($this->type_id && ($type = Type::find($this->type_id)) && $type && $type->slug === 'quiz') {
            foreach (range(1, 4) as $pos) {
                $answer = trim($this->input("answer{$pos}"));
                $checked = (bool) $this->input("answer{$pos}_correct");

                if ($answer) {
                    $options[] = [
                        'value' => $answer,
                        'is_correct' => $checked,
                        'order' => count($options) + 1,
                    ];
                }
            }
        }

        $this->merge([
            'nick' => $nick,
            'options' => $options,
            'ip_address' => $this->getRealIpAddress(),
            'user_agent' => $this->userAgent(),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'type_id' => 'required|exists:types,id',
            'group_id' => 'nullable|exists:groups,id',
            'nick' => 'nullable|string|max:25|regex:/^[a-zA-Z0-9_]+$/',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1024',
            'image' => 'nullable|image|max:2048',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string|max:255',
        ];

        // Validaciones condicionales para quiz
        if ($this->type_id && $this->isQuizType()) {
            $rules['options'] = 'required|array|min:2|max:4';
            $rules['options.*.value'] = 'required|string|max:255';
            $rules['options.*.is_correct'] = 'required|boolean';
            $rules['options.*.order'] = 'required|integer|min:1|max:4';
        } else {
            $rules['options'] = 'nullable|array';
        }

        return $rules;
    }

    /**
     * Verifica si el tipo seleccionado es quiz
     */
    private function isQuizType(): bool
    {
        if (!$this->type_id) {
            return false;
        }

        $type = Type::find($this->type_id);
        return $type && $type->slug === 'quiz';
    }

    public function messages()
    {
        return [
            'type_id.required' => 'Debes seleccionar un tipo',
            'type_id.exists' => 'El tipo seleccionado no es válido',
            'title.required' => 'El título es obligatorio',
            'title.max' => 'El título no debe superar los :max caracteres',
            'group_id.exists' => 'El Grupo debe existir',
            'content.required' => 'El contenido es obligatorio',
            'content.max' => 'El contenido no debe superar los :max caracteres',
            'image.image' => 'El archivo debe ser una imagen, preferiblemente webp, jpg o png',
            'image.max' => 'La imagen no debe superar los :max kilobytes',
            'nick.max' => 'El nick no debe superar los :max caracteres',
            'nick.regex' => 'El nick solo puede contener letras, números y guiones bajos sin @',
            'throttle' => 'Has enviado demasiadas sugerencias recientemente. Por favor, espera un momento antes de enviar otra.',

            // Mensajes para options
            'options.required' => 'Para preguntas tipo quiz debes añadir las respuestas.',
            'options.min' => 'Para preguntas tipo quiz debes añadir al menos :min respuestas.',
            'options.max' => 'Para preguntas tipo quiz puedes añadir máximo :max respuestas.',
            'options.*.value.required' => 'El valor de la respuesta es obligatorio.',
            'options.*.value.max' => 'El valor de la respuesta no debe superar los :max caracteres.',
            'options.*.is_correct.required' => 'Debes indicar si la respuesta es correcta o no.',
            'options.*.is_correct.boolean' => 'El valor de respuesta correcta debe ser verdadero o falso.',
            'options.*.order.required' => 'El orden de la respuesta es obligatorio.',
            'options.*.order.integer' => 'El orden debe ser un número entero.',
            'options.*.order.min' => 'El orden debe ser mínimo :min.',
            'options.*.order.max' => 'El orden debe ser máximo :max.',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Helpers\IpHelper;
use App\Models\Type;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Request para envíos de sugerencia de contenido
 *
 * @param  int  $type_id  Id del tipo de contenido asociado
 * @param  string  $nick  Nombre del usuario en las redes sociales con formato de username o nick
 * @param  string  $title  Título principal del contenido
 * @param  string  $content  Contenido
 * @param  array  $options  Opciones para quiz (solo requerido si type_id es quiz)
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
     * Configure the validator instance.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $ip = IpHelper::getClientIp($this);
            $throttleKey = 'suggestions:'.$ip;

            // Si hay demasiados intentos, añado un error de validación
            if (! RateLimiter::attempt($throttleKey, 1, fn () => true, 10)) {
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
        if (! $this->type_id) {
            return;
        }

        $type = Type::find($this->type_id);
        if (! $type || $type->slug !== 'quiz') {
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
        $correctAnswers = array_filter($options, fn ($option) => $option['is_correct'] ?? false);
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
        $ip = IpHelper::getClientIp($this);
        $throttleKey = 'suggestions:'.$ip;
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

        $type = $this->type_id ? Type::find($this->type_id) : null;

        if ($type instanceof Type && $type->slug === 'quiz') {
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
            'ip_address' => IpHelper::getClientIp($this),
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
            'group_id' => [
                'nullable',
                'integer',
                Rule::exists('groups', 'id')->where(
                    fn ($query) => $query->where('type_id', $this->input('type_id'))
                ),
            ],
            'nick' => 'nullable|string|max:25|regex:/^[a-zA-Z0-9_]+$/',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1024',
            'image' => [
                'nullable',
                'file',
                'image',
                'mimes:webp,jpeg,jpg,png',
                'mimetypes:image/webp,image/jpeg,image/png',
                'max:2048',
            ],
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string|max:255',
            'terms' => 'accepted',
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
        if (! $this->type_id) {
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
            'group_id.integer' => 'El grupo seleccionado no es válido.',
            'group_id.exists' => 'El grupo seleccionado no pertenece al tipo de contenido indicado.',
            'content.required' => 'El contenido es obligatorio',
            'content.max' => 'El contenido no debe superar los :max caracteres',
            'image.image' => 'El archivo debe ser una imagen en formato JPEG, PNG o WebP',
            'image.mimes' => 'La imagen debe ser de tipo JPG, PNG o WebP',
            'image.mimetypes' => 'La imagen debe ser de tipo JPG, PNG o WebP',
            'image.max' => 'La imagen no debe superar los :max kilobytes',
            'nick.max' => 'El nick no debe superar los :max caracteres',
            'nick.regex' => 'El nick solo puede contener letras, números y guiones bajos sin @',
            'terms.accepted' => 'Debes aceptar las normas, la política de privacidad y las condiciones de uso.',
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

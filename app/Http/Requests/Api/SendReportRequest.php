<?php

namespace App\Http\Requests\Api;

use App\Helpers\IpHelper;
use App\Models\Content;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SendReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'content_id' => 'required|integer|exists:contents,id',
            'title' => 'required|string|max:255',
            'type' => 'nullable|string|in:' . implode(',', array_keys(Report::getTypes())),
            'description' => 'nullable|string|max:1024',
            'additional_info' => 'nullable|string|max:1024',
            'reporter_name' => 'nullable|string|max:255',
            'reporter_email' => 'nullable|email|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'content_id.required' => 'El ID del contenido es requerido.',
            'content_id.exists' => 'El contenido especificado no existe.',
            'title.required' => 'El título del reporte es requerido.',
            'title.max' => 'El título no puede exceder los 255 caracteres.',
            'type.in' => 'El tipo de reporte no es válido.',
            'description.max' => 'La descripción no puede exceder los 1024 caracteres.',
            'additional_info.max' => 'La información adicional no puede exceder los 1024 caracteres.',
            'reporter_name.max' => 'El nombre del reporter no puede exceder los 255 caracteres.',
            'reporter_email.email' => 'El email del reporter debe ser una dirección válida.',
            'reporter_email.max' => 'El email del reporter no puede exceder los 255 caracteres.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_id' => Auth::id(),
            'ip_address' => IpHelper::getClientIp($this),
            'user_agent' => $this->userAgent(),
            'assigned_to' => $this->getFirstAdminUser(),
            'reportable_type' => Content::class,
            'reportable_id' => $this->content_id,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function validated($key = null, $default = null)
    {
        $validated = parent::validated($key, $default);

        if ($key !== null) {
            return $validated;
        }

        // Añadir los campos adicionales que preparamos
        $validated['user_id'] = $this->user_id;
        $validated['reporter_ip'] = $this->ip_address;
        $validated['assigned_to'] = $this->assigned_to;
        $validated['reportable_type'] = $this->reportable_type;
        $validated['reportable_id'] = $this->reportable_id;

        return $validated;
    }

    /**
     * Obtener el primer usuario administrador
     */
    private function getFirstAdminUser(): ?int
    {
        $admin = User::where('role_id', 1)->first();
        return $admin ? $admin->id : null;
    }

    /**
     * Parámetros del body para la documentación generada por Scribe.
     */
    public function bodyParameters(): array
    {
        return [
            'content_id' => [
                'description' => 'ID del contenido a reportar.',
                'example' => 123,
            ],
            'title' => [
                'description' => 'Título breve del reporte (máximo 255 caracteres).',
                'example' => 'Contenido inapropiado',
            ],
            'type' => [
                'description' => 'Tipo de reporte (spam, inappropriate_content, adult_content, hate_speech, copyright, violence, harassment, misinformation, other).',
                'example' => 'inappropriate_content',
            ],
            'description' => [
                'description' => 'Descripción detallada del motivo del reporte (máximo 1024 caracteres).',
                'example' => 'Este contenido incumple las normas de la comunidad.',
            ],
            'additional_info' => [
                'description' => 'Información adicional opcional sobre el contexto del reporte.',
                'example' => 'Reportado tras revisión de moderación.',
            ],
            'reporter_name' => [
                'description' => 'Nombre visible del informante si difiere del usuario autenticado.',
                'example' => 'Juan Pérez',
            ],
            'reporter_email' => [
                'description' => 'Correo de contacto del informante si difiere del usuario autenticado.',
                'example' => 'usuario@ejemplo.com',
            ],
        ];
    }
}

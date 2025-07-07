<?php

namespace App\Http\Requests\Api;

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
            'ip_address' => $this->getRealIpAddress(),
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

        // Añadir los campos adicionales que preparamos
        $validated['user_id'] = $this->user_id;
        $validated['reporter_ip'] = $this->ip_address;
        $validated['assigned_to'] = $this->assigned_to;
        $validated['reportable_type'] = $this->reportable_type;
        $validated['reportable_id'] = $this->reportable_id;

        return $validated;
    }

    /**
     * Obtener la IP real del cliente considerando Cloudflare
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

    /**
     * Obtener el primer usuario administrador
     */
    private function getFirstAdminUser(): ?int
    {
        $admin = User::where('role_id', 1)->first();
        return $admin ? $admin->id : null;
    }
}

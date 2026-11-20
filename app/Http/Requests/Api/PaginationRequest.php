<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Paginación de resultados
 *
 * @param  int  $page  Número de página que se desea obtener
 * @param  int  $limit  Cantidad de resultados que se desea obtener
 */
class PaginationRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'page' => 'sometimes|integer|min:1',
            'limit' => 'sometimes|integer|min:1|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'page.integer' => 'La página debe ser un número entero.',
            'page.min' => 'La página debe ser mayor a 0.',
            'limit.integer' => 'El límite debe ser un número entero.',
            'limit.min' => 'El límite debe ser mayor a 0.',
            'limit.max' => 'El límite no puede ser mayor a 50.',
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
     * Get validated page number
     */
    public function getPage(): int
    {
        return (int) ($this->validated()['page'] ?? 1);
    }

    /**
     * Get validated limit
     */
    public function getLimit(): int
    {
        return (int) ($this->validated()['limit'] ?? 20);
    }

    /**
     * Parámetros de query para documentación API
     */
    public function queryParameters(): array
    {
        return [
            'page' => [
                'description' => 'Número de página a obtener',
                'example' => 1,
            ],
            'limit' => [
                'description' => 'Cantidad de elementos por página (máximo 50)',
                'example' => 2,
            ],
        ];
    }
}

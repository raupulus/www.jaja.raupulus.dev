<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * Paginación de resultados
 *
 * @param int $limit Cantidad de resultados que se desea obtener
 */
class ContentRandomRequest extends FormRequest
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
            'limit' => 'sometimes|integer|min:1|max:5',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'limit.integer' => 'El límite debe ser un número entero.',
            'limit.min' => 'El límite debe ser mayor a 0.',
            'limit.max' => 'El límite no puede ser mayor a 5.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'limit' => (int) $this->input('limit', 1),
        ]);
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
     * Get validated limit
     */
    public function getLimit(): int
    {
        return $this->validated()['limit'];
    }

    /**
     * Parámetros de query para documentación API
     */
    public function queryParameters(): array
    {
        return [
            'limit' => [
                'description' => 'Cantidad de elementos por página (máximo 5)',
                'example' => 1,
            ],
        ];
    }

}

<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;


class PaginationRequest extends FormRequest
{
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
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:50',
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
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'page' => (int) $this->input('page', 1),
            'limit' => (int) $this->input('limit', 20),
        ]);
    }

    /**
     * Handle a failed validation attempt for API.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    /**
     * Get validated page number
     */
    public function getPage(): int
    {
        return $this->validated()['page'];
    }

    /**
     * Get validated limit
     */
    public function getLimit(): int
    {
        return $this->validated()['limit'];
    }
}

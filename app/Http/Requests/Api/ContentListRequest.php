<?php

namespace App\Http\Requests\Api;

use App\Http\Traits\ApiResponseTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ContentListRequest extends FormRequest
{
    use ApiResponseTrait;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'after_id' => 'sometimes|integer|min:0',
            'limit' => 'sometimes|integer|min:1|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'after_id.integer' => 'El campo after_id debe ser un número entero.',
            'after_id.min' => 'El campo after_id debe ser al menos 0.',
            'limit.integer' => 'El límite debe ser un número entero.',
            'limit.min' => 'El límite debe ser mayor a 0.',
            'limit.max' => 'El límite no puede ser mayor a 100.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'after_id' => $this->input('after_id') !== null ? (int) $this->input('after_id') : 0,
            'limit' => $this->input('limit') !== null ? (int) $this->input('limit') : 25,
        ]);
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->errorResponse('Errores de validación', 422, $validator->errors())
        );
    }

    public function getAfterId(): ?int
    {
        return $this->validated()['after_id'] ?? null;
    }

    public function getLimit(): int
    {
        return $this->validated()['limit'] ?? 25;
    }
}

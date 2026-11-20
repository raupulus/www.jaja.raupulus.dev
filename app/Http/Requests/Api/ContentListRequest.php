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
            'exclude_groups' => 'sometimes|array|max:50',
            'exclude_groups.*' => 'integer|exists:groups,id',
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
            'exclude_groups.array' => 'El campo exclude_groups debe ser un arreglo.',
            'exclude_groups.max' => 'No puedes excluir más de 50 grupos por petición.',
            'exclude_groups.*.integer' => 'Los grupos a excluir deben ser números enteros.',
            'exclude_groups.*.exists' => 'Uno de los grupos a excluir no existe.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->errorResponse('Errores de validación', 422, $validator->errors())
        );
    }

    public function getAfterId(): ?int
    {
        return isset($this->validated()['after_id']) ? (int) $this->validated()['after_id'] : null;
    }

    public function getLimit(): int
    {
        return (int) ($this->validated()['limit'] ?? 25);
    }

    public function getExcludedGroups(): array
    {
        return $this->validated()['exclude_groups'] ?? [];
    }

    /**
     * Parámetros de query para la documentación generada por Scribe.
     */
    public function queryParameters(): array
    {
        return [
            'after_id' => [
                'description' => 'ID a partir del cual se desean obtener los chistes.',
                'example' => 0,
            ],
            'limit' => [
                'description' => 'Cantidad de chistes por página (máximo 100).',
                'example' => 25,
            ],
            'exclude_groups' => [
                'description' => 'Lista de IDs numéricos de grupos a excluir.',
                'example' => [4, 14],
            ],
        ];
    }
}

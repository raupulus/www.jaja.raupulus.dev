<?php

namespace App\Http\Traits;

use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{
    protected function successResponse(
        mixed  $data = null,
        string $message = 'Operación Exitosa',
        int    $status = 200,
        array  $meta = []
    ): JsonResponse
    {
        return ApiResponse::success($data, $message, $status, $meta);
    }

    protected function errorResponse(
        string $message = 'Error en la operación',
        int    $status = 400,
        mixed  $errors = null,
        array  $meta = []
    ): JsonResponse
    {
        return ApiResponse::error($message, $status, $errors, $meta);
    }

    protected function paginatedResponse(
        LengthAwarePaginator $paginator,
        string               $message = 'Datos Obtenidos con éxito.',
        array                $meta = []
    ): JsonResponse
    {
        return ApiResponse::paginated($paginator, $message, $meta);
    }

    protected function collectionResponse(
        JsonResource $collection,
        string       $message = 'Datos Obtenidos con éxito.',
        array        $meta = []
    ): JsonResponse
    {
        return ApiResponse::collection($collection, $message, $meta);
    }
}

<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    /**
     * Respuesta con éxito.
     *
     * @param mixed|null $data
     * @param string $message
     * @param int $status
     * @param array $meta
     * @return JsonResponse
     */
    public static function success(
        mixed  $data = null,
        string $message = 'Operación Exitosa',
        int    $status = 200,
        array  $meta = []
    ): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
            'message' => $message,
        ];

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $status);
    }

    /**
     * Respuesta con error.
     *
     * @param string $message
     * @param int $status
     * @param mixed|null $errors
     * @param array $meta
     *
     * @return JsonResponse
     */
    public static function error(
        string $message = 'Error en la operación',
        int    $status = 400,
        mixed  $errors = null,
        array  $meta = []
    ): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        if (!empty($meta)) {
            $response['meta'] = $meta;
        }

        /*
        if (config('app.debug')) {
            $response['debug'] = [
                'request' => request()->all(),
                'session' => session()?->all(),
                'cookies' => request()->cookies->all(),
                'server' => $_SERVER,
                'headers' => getallheaders(),
            ];
        }
        */

        return response()->json($response, $status);
    }

    /**
     * Respuesta con paginación.
     *
     * @param LengthAwarePaginator $paginator
     * @param string $message
     * @param array $meta
     * @return JsonResponse
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        string               $message = 'Datos Obtenidos con éxito.',
        array                $meta = []
    ): JsonResponse
    {
        return self::success($paginator->items(), $message, 200, array_merge($meta, [
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'last_page' => $paginator->lastPage(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                    'has_more_pages' => $paginator->hasMorePages(),
                    'has_previous_page' => $paginator->currentPage() > 1,
                    'next_page_url' => $paginator->nextPageUrl(),
                    'prev_page_url' => $paginator->previousPageUrl(),
                ]
            ])
        );
    }

    /**
     * Respuesta con colección de recursos.
     *
     * @param JsonResource $collection
     * @param string $message
     * @param array $meta
     * @return JsonResponse
     */
    public static function collection(
        JsonResource $collection,
        string       $message = 'Datos Obtenidos con éxito.',
        array        $meta = []
    ): JsonResponse
    {
        return self::success($collection->resolve(), $message, 200, $meta);
    }

}

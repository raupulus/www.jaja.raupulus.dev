<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Category;
use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class v1 extends Controller
{

    /**
     * Devuelve un contenido aleatorio.
     *
     * @param int|null $limit
     * @return JsonResponse
     */
    public function random(int|null $limit = 1): JsonResponse
    {
        return response()->json([
            'data' => ['random: todo ok!!!'],
        ]);
    }

    /**
     * Devuelve la lista de tipos de contenido que existen.
     *
     * @return JsonResponse
     */
    public function typesList(): JsonResponse
    {
        return response()->json([
            'data' => ['typesList: todo ok!!!'],
        ]);
    }

    /**
     * Devuelve un contenido aleatorio de un tipo.
     *
     * @param Type $type
     * @return JsonResponse
     */
    public function getContentRandomFromType(Type $type): JsonResponse
    {
        return response()->json([
            'data' => ['getContentRandomFromType: todo ok!!!', $type],
        ]);
    }

    /**
     * Devuelve la lista de categorías disponibles.
     *
     * @return JsonResponse
     */
    public function categoriesList(): JsonResponse
    {
        return response()->json([
            'data' => ['categoriesList: todo ok!!!'],
        ]);
    }

    /**
     * Devuelve un contenido aleatorio que pertenezca al tipo y categoría recibido.
     *
     * @param Type $type
     * @param Category $category
     * @return JsonResponse
     */
    public function getContentRandomFromCategory(Type $type, Category $category): JsonResponse
    {
        return response()->json([
            'data' => ['getContentRandomFromCategory: todo ok!!!', $type, $category],
        ]);
    }

}

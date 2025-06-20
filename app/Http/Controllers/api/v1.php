<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\PaginationRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ContentResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\TypeResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;
use App\Models\Content;
use App\Models\Group;
use App\Models\Type;
use Illuminate\Http\JsonResponse;

class v1 extends Controller
{
    use ApiResponseTrait;

    /**
     * Devuelve un contenido aleatorio.
     *
     * @param int|null $limit Cantidad de elementos a devolver, máximo 5 al ser ruta pública.
     * @return JsonResponse
     */
    public function random(int|null $limit = 1): JsonResponse
    {
        try {
            $contents = Content::inRandomOrder();

            return $this->collectionResponse(
                ContentResource::collection($contents->limit($limit)->get()),
                'Se obtuvieron ' . $limit . ' contenidos aleatorios',
                [
                    'total_items' => $contents->count(),
                    'limit' => $limit,
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener chistes aleatorios', 500);
        }
    }

    /**
     * Devuelve la lista de tipos de contenido que existen.
     *
     * @return JsonResponse
     */
    public function typesIndex(): JsonResponse
    {
        try {
            $types = Type::orderByDesc('name')->get();

            if ( ! $types->count() ) {
                return $this->errorResponse('No se encontraron tipos de contenido', 404);
            }

            return $this->successResponse(
                TypeResource::collection($types),
                'Se obtuvieron ' . $types->count() . ' tipos',
                200,
                [
                    'total_items' => $types->count(),
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener la lista de tipos', 500);
        }
    }

    /**
     * Devuelve la lista de grupos de contenido que existen paginados.
     *
     * @param PaginationRequest $request
     * @return JsonResponse
     */
    public function groupsIndex(PaginationRequest $request): JsonResponse
    {
        try {
            $groups = Group::orderByDesc('title')->paginate($request->getLimit(), ['*'], 'groups_index', $request->getPage());

            return $this->collectionPaginatedResponse(
                $groups,
                GroupResource::collection($groups->items()),
                'Se devuelven ' . $groups->count() . ' grupos',
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener la lista de grupos', 500);
        }
    }

    /**
     * Devuelve la lista de categorías disponibles.
     *
     * @param PaginationRequest $request
     * @return JsonResponse
     */
    public function categoriesIndex(PaginationRequest $request): JsonResponse
    {
        $page = $request->getPage();
        $limit = $request->getLimit();

        try {
            $categories = Category::orderByDesc('title')
                ->paginate($limit, ['*'], 'page', $page);

            if ($page > $categories->lastPage() && $categories->total() > 0) {
                return $this->errorResponse(
                    'La página solicitada no existe. Última página disponible: ' . $categories->lastPage(),
                    404
                );
            }

            return $this->collectionPaginatedResponse(
                $categories,
                CategoryResource::collection($categories->items()),
                'Se obtuvieron ' . $categories->count() . ' categorías de ' . $categories->total() . ', página ' . $page . '.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener la lista de categorías', 500);
        }
    }

    /**
     * Devuelve un contenido aleatorio de un tipo.
     *
     * @param Type $type Tipo de contenido por el que se filtra.
     * @param int $limit Cantidad de elementos a devolver.
     * @return JsonResponse
     */
    public function getContentRandomFromType(Type $type, int $limit = 1): JsonResponse
    {
        try {
            $contents = Content::byType($type)->random();
            $total = $contents->count();

            if ( ! $total ) {
                return $this->errorResponse(
                    'No se encontraron contenidos para el tipo especificado',
                    404
                );
            }

            return $this->collectionResponse(
                ContentResource::collection($contents->limit($limit)->get()),
                'Se devuelve ' . $limit . ' contenido aleatorio para el tipo ' . $type->name . ' de ' . $total . ' contenidos totales para este tipo.',
                [
                    'type' => $type->name,
                    'type_slug' => $type->slug,
                    'total_items' => $total,
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener contenidos del tipo especificado', 500);
        }
    }

    /**
     * Devuelve un contenido aleatorio que pertenezca al tipo y categoría recibido.
     *
     * @param Type $type Tipo de elemento por el que se filtra.
     * @param string $categorySlug Slug de la Categoría por la que se filtra.
     * @param int $limit Cantidad de elementos a devolver.
     * @return JsonResponse
     */
    public function getContentRandomFromCategory(Type $type, string $categorySlug, int $limit = 1): JsonResponse
    {
        try {
            $category = Category::where('slug', $categorySlug)->first();

            if (!$category) {
                return $this->errorResponse('Categoría no encontrada', 404);
            }

            $contents = Content::byTypeAndCategory($type, $category)->random();
            $total = $contents->count();

            if ( ! $total ) {
                return $this->errorResponse(
                    'No se encontraron contenidos para el tipo y categoría especificados',
                    404
                );
            }

            return $this->collectionResponse(
                ContentResource::collection($contents->limit($limit)->get()),
                'Se devuelve ' . $limit . ' contenido aleatorio para el tipo ' . $type->name . ' y la categoría ' . $category->title . ' de ' . $total . ' contenidos totales para estos.',
                [
                    'type' => $type->name,
                    'type_slug' => $type->slug,
                    'category' => $category->title,
                    'category_slug' => $category->slug,
                    'total_items' => $total,
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener contenidos del tipo especificado', 500);
        }
    }

    /**
     * Contenido aleatorio que pertenecen al grupo recibido.
     *
     * @param Group $group Grupo por el que se filtra.
     * @param int $limit Cantidad de elementos a devolver.
     *
     * @return JsonResponse
     */
    public function getContentRandomFromGroup(Group $group, int $limit = 1): JsonResponse
    {
        try {
            $contents = Content::byGroup($group)->random();
            $total = $contents->count();

            if ( ! $total ) {
                return $this->errorResponse(
                    'No se encontraron contenidos para el grupo especificado',
                    404
                );
            }

            return $this->collectionResponse(
                ContentResource::collection($contents->limit($limit)->get()),
                'Se devuelve ' . $limit . ' contenido aleatorio para el grupo ' . $group->title . ' de ' . $total . ' contenidos totales para este.',
                [
                    'group' => $group->title,
                    'group_slug' => $group->slug,
                    'total_items' => $total,
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener contenidos del tipo especificado', 500);
        }
    }

}

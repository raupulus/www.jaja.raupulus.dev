<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ContentRandomFromGroupRequest;
use App\Http\Requests\Api\ContentRandomFromTypeAndCategoryRequest;
use App\Http\Requests\Api\ContentRandomFromTypeRequest;
use App\Http\Requests\Api\ContentRandomRequest;
use App\Http\Requests\Api\PaginationRequest;
use App\Http\Requests\Api\SendReportRequest;
use App\Http\Requests\Api\SendSuggestionRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ContentResource;
use App\Http\Resources\GroupResource;
use App\Http\Resources\TypeResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;
use App\Models\Content;
use App\Models\Group;
use App\Models\Report;
use App\Models\Suggestion;
use App\Models\Type;
use Illuminate\Http\JsonResponse;

class V1Controller extends Controller
{
    use ApiResponseTrait;

    /**
     * Contenido Aleatorio
     *
     * Devuelve un contenido aleatorio de entre todos los existentes en la plataforma sin filtro alguno.
     *
     * Este endpoint al ser público está limitado a máximo 5 elementos por petición y a 10 peticiones por minuto.
     *
     * @group 📚 Contenidos
     * @unauthenticated
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data array Colección de contenidos aleatorios
     * @responseField data[].title string Título del contenido (chiste, adivinanza, etc.)
     * @responseField data[].content string Texto del contenido
     * @responseField data[].urlImage string|null URL completa de la imagen asociada al contenido (null si no tiene imagen)
     * @responseField data[].uploader string Nombre del usuario que subió el contenido
     * @responseField meta object Metadatos adicionales de la respuesta
     * @responseField meta.total_items integer Número total de contenidos disponibles
     * @responseField meta.limit integer Límite aplicado en esta consulta
     *
     * @response 404 {
     *   "success": false,
     *   "message": "No se encontraron contenidos"
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Error al obtener chistes aleatorios"
     * }
     *
     * @param ContentRandomRequest $request
     * @return JsonResponse
     */
    public function random(ContentRandomRequest $request): JsonResponse
    {
        $limit = $request->getLimit();

        try {
            $contentsQuery = Content::whereNotIn('group_id', [4, 14])
                ->whereNot('is_adult')
                ->inRandomOrder();
            $count = $contentsQuery->count();

            if (!$count) {
                return $this->errorResponse('No se encontraron contenidos', 404);
            }

            $contents = $contentsQuery->limit($limit)->get();

            if ($contents->count() > 1) {
                $message = 'Se obtuvieron ' . $contents->count() . ' contenidos aleatorios';
            } else {
                $message = 'Se obtuvo ' . $contents->count() . ' contenido aleatorio';
            }

            return $this->collectionResponse(
                ContentResource::collection($contents),
                $message,
                [
                    'total_items' => $count,
                    'limit' => $limit,
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener chistes aleatorios', 500);
        }
    }

    /**
     * Tipos de Contenido
     *
     * Devuelve la lista de tipos de contenido que existen.
     *
     * Útil para utilizar el slug del tipo que necesites y filtrar en otros endpoints.
     *
     * @group 🏷️ Categorías, Grupos y Tipos
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data array Lista de tipos de contenido disponibles
     * @responseField data[].name string Nombre del tipo de contenido
     * @responseField data[].slug string Slug del tipo para URLs amigables
     * @responseField data[].description string Descripción del tipo de contenido
     * @responseField data[].urlImage string|null URL completa de la imagen asociada al tipo (null si no tiene imagen)
     * @responseField meta object Metadatos adicionales de la respuesta
     * @responseField meta.total_items integer Número total de tipos disponibles
     *
     * @response 404 {
     *   "success": false,
     *   "message": "No se encontraron tipos de contenido"
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Error al obtener la lista de tipos"
     * }
     *
     * @return JsonResponse
     */
    public function typesIndex(): JsonResponse
    {
        try {
            $types = Type::select(['id', 'name', 'slug', 'description', 'image'])
                ->orderByDesc('name')
                ->get();

            if (!$types->count()) {
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
     * Grupos de Contenidos
     *
     * Devuelve la lista de grupos de contenido que existen paginados.
     *
     * @group 🏷️ Categorías, Grupos y Tipos
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data array Lista de grupos de contenido paginados
     * @responseField data[].title string Título del grupo
     * @responseField data[].slug string Slug del grupo para URLs amigables
     * @responseField data[].urlImage string|null URL completa de la imagen asociada al grupo (null si no tiene imagen)
     * @responseField pagination object Información de paginación
     * @responseField pagination.current_page integer Página actual
     * @responseField pagination.first_page_url string URL de la primera página
     * @responseField pagination.from integer Número del primer elemento en la página actual
     * @responseField pagination.last_page integer Número de la última página
     * @responseField pagination.last_page_url string URL de la última página
     * @responseField pagination.next_page_url string|null URL de la siguiente página (null si es la última)
     * @responseField pagination.path string URL base para la paginación
     * @responseField pagination.per_page integer Elementos por página
     * @responseField pagination.prev_page_url string|null URL de la página anterior (null si es la primera)
     * @responseField pagination.to integer Número del último elemento en la página actual
     * @responseField pagination.total integer Total de elementos disponibles
     *
     * @response 404 {
     *   "success": false,
     *   "message": "La página solicitada no existe. Última página disponible: 2"
     * }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "No se encontraron grupos de contenido"
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Error al obtener la lista de grupos"
     * }
     *
     * @param PaginationRequest $request
     * @return JsonResponse
     */
    public function groupsIndex(PaginationRequest $request): JsonResponse
    {
        try {
            $page = $request->getPage();
            $limit = $request->getLimit();

            $groups = Group::select(['id', 'title', 'slug', 'image'])
                ->orderByDesc('title')
                ->paginate($limit, ['*'], 'groups_index', $page);

            if ($page > $groups->lastPage() && $groups->total() > 0) {
                return $this->errorResponse(
                    'La página solicitada no existe. Última página disponible: ' . $groups->lastPage(),
                    404
                );
            }

            $count = $groups->count();

            if (!$count) {
                return $this->errorResponse('No se encontraron grupos de contenido', 404);
            }

            if ($count > 1) {
                $message = 'Se obtuvieron ' . $count . ' grupos de contenido';
            } else {
                $message = 'Se obtuvo ' . $count . ' grupo de contenido';
            }

            return $this->collectionPaginatedResponse(
                $groups,
                GroupResource::collection($groups->items()),
                $message
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener la lista de grupos', 500);
        }
    }

    /**
     * Categorías
     *
     * Devuelve la lista de categorías disponibles paginadas.
     *
     * @group 🏷️ Categorías, Grupos y Tipos
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data array Lista de categorías paginadas
     * @responseField data[].title string Título de la categoría
     * @responseField data[].slug string Slug de la categoría para URLs amigables
     * @responseField data[].description string Descripción de la categoría
     * @responseField data[].urlImage string|null URL completa de la imagen asociada a la categoría (null si no tiene imagen)
     * @responseField pagination object Información de paginación
     * @responseField pagination.current_page integer Página actual
     * @responseField pagination.first_page_url string URL de la primera página
     * @responseField pagination.from integer Número del primer elemento en la página actual
     * @responseField pagination.last_page integer Número de la última página
     * @responseField pagination.last_page_url string URL de la última página
     * @responseField pagination.next_page_url string|null URL de la siguiente página (null si es la última)
     * @responseField pagination.path string URL base para la paginación
     * @responseField pagination.per_page integer Elementos por página
     * @responseField pagination.prev_page_url string|null URL de la página anterior (null si es la primera)
     * @responseField pagination.to integer Número del último elemento en la página actual
     * @responseField pagination.total integer Total de elementos disponibles
     *
     * @response 404 {
     *   "success": false,
     *   "message": "No se han encontrado categorías"
     * }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "La página solicitada no existe. Última página disponible: 5"
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Error al obtener la lista de categorías"
     * }
     *
     * @param PaginationRequest $request
     * @return JsonResponse
     */
    public function categoriesIndex(PaginationRequest $request): JsonResponse
    {
        try {
            $page = $request->getPage();
            $limit = $request->getLimit();

            $categories = Category::select(['id', 'title', 'slug', 'description', 'image'])
                ->orderByDesc('title')
                ->paginate($limit, ['*'], 'page', $page);

            if ($page > $categories->lastPage() && $categories->total() > 0) {
                return $this->errorResponse(
                    'La página solicitada no existe. Última página disponible: ' . $categories->lastPage(),
                    404
                );
            }

            $count = $categories->count();

            if (!$count) {
                return $this->errorResponse('No se han encontrado categorías', 404);
            }

            if ($count > 1) {
                $message = 'Se obtuvieron ' . $count . ' categorías de ' . $categories->total() . ', página ' . $page . '.';
            } else {
                $message = 'Se obtuvo ' . $count . ' categoría de ' . $categories->total() . ', página ' . $page . '.';
            }

            return $this->collectionPaginatedResponse(
                $categories,
                CategoryResource::collection($categories->items()),
                $message
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener la lista de categorías', 500);
        }
    }

    /**
     * Contenido en base a un tipo
     *
     * Devuelve un contenido aleatorio de un tipo concreto recibido.
     *
     * @group 📚 Contenidos
     * @urlParam type_slug string required El slug del tipo de contenido. Example: chistes
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data array Lista con el contenido aleatorio solicitado
     * @responseField data[].title string Título del contenido
     * @responseField data[].content string Texto del contenido (chiste, adivinanza, etc.)
     * @responseField data[].urlImage string|null URL completa de la imagen asociada al contenido (null si no tiene imagen)
     * @responseField data[].uploader string Nick del usuario que subió el contenido (formato: @nick)
     * @responseField meta object Metadatos adicionales de la respuesta
     * @responseField meta.type string Nombre del tipo de contenido
     * @responseField meta.type_slug string Slug del tipo
     * @responseField meta.total_items integer Total de contenidos disponibles para este tipo
     *
     * @response 404 {
     *   "success": false,
     *   "message": "No se encontraron contenidos para el tipo especificado"
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Error al obtener contenidos del tipo especificado"
     * }
     *
     * @param Type $type Tipo de contenido por el que se filtra.
     * @return JsonResponse
     */
    public function getContentRandomFromType(ContentRandomFromTypeRequest $request, Type $type): JsonResponse
    {
        try {
            $limit = $request->getLimit();

            $contents = Content::whereNotIn('group_id', [4, 14])
                ->whereNot('is_adult')
                ->select(['id', 'title', 'content', 'image', 'uploaded_by', 'user_id'])
                ->byType($type)
                ->random();

            $count = $contents->count();

            if (!$count) {
                return $this->errorResponse(
                    'No se encontraron contenidos para el tipo especificado',
                    404
                );
            }

            if ($count > 1 && $limit > 1) {
                $message = 'Se devuelven ' . $limit . ' contenidos aleatorios para el tipo ' . $type->name . ' de ' . $count . ' contenidos totales para este tipo.';
            } else if ($count > 1 && $limit == 1) {
                $message = 'Se devuelve ' . $limit . ' contenido aleatorio para el tipo ' . $type->name . ' de ' . $count . ' contenidos totales para este tipo.';
            } else if ($count == 1 && $limit > 1) {
                $message = 'Se devuelven ' . $limit . ' contenidos aleatorios para el tipo ' . $type->name . ' de ' . $count . ' contenido total para este tipo.';
            } else {
                $message = 'Se devuelve ' . $limit . ' contenido aleatorio para el tipo ' . $type->name . ' de ' . $count . ' contenido total para este tipo.';
            }

            return $this->collectionResponse(
                ContentResource::collection($contents->limit($limit)->get()),
                $message,
                [
                    'type' => $type->name,
                    'type_slug' => $type->slug,
                    'total_items' => $count,
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener contenidos del tipo especificado', 500);
        }
    }

    /**
     * Contenido en base a un tipo y Categoría
     *
     * Devuelve un contenido aleatorio que pertenezca al tipo y categoría recibido.
     *
     * @group 📚 Contenidos
     *
     * @urlParam type_slug string required El slug del tipo de contenido. Example: chistes
     * @urlParam categorySlug string required El slug de la categoría. Example: javascript
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data array Lista con el contenido aleatorio solicitado
     * @responseField data[].title string Título del contenido
     * @responseField data[].content string Texto del contenido (chiste, adivinanza, etc.)
     * @responseField data[].urlImage string|null URL completa de la imagen asociada al contenido (null si no tiene imagen)
     * @responseField data[].uploader string Nick del usuario que subió el contenido (formato: @nick)
     * @responseField meta object Metadatos adicionales de la respuesta
     * @responseField meta.type string Nombre del tipo de contenido
     * @responseField meta.type_slug string Slug del tipo
     * @responseField meta.category string Título de la categoría
     * @responseField meta.category_slug string Slug de la categoría
     * @responseField meta.total_items integer Total de contenidos disponibles para este tipo y categoría
     *
     * @response 404 {
     *   "success": false,
     *   "message": "Categoría no encontrada"
     * }
     *
     * @response 404 {
     *   "success": false,
     *   "message": "No se encontraron contenidos para el tipo y categoría especificados"
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Error al obtener contenidos del tipo especificado"
     * }
     *
     * @param ContentRandomFromTypeAndCategoryRequest $request
     * @param Type $type Tipo de elemento por el que se filtra.
     * @param string $categorySlug Slug de la Categoría por la que se filtra.
     * @return JsonResponse
     */
    public function getContentRandomFromCategory(ContentRandomFromTypeAndCategoryRequest $request, Type $type, string $categorySlug): JsonResponse
    {
        try {
            $category = Category::where('slug', $categorySlug)->first();

            if (!$category) {
                return $this->errorResponse('Categoría no encontrada', 404);
            }

            $limit = $request->getLimit();

            $contents = Content::whereNotIn('group_id', [4, 14])
                ->whereNot('is_adult')
                ->select(['id', 'title', 'content', 'image', 'uploaded_by', 'user_id', 'group_id'])
                ->byTypeAndCategory($type, $category)
                ->random();

            $count = $contents->count();

            if (!$count) {
                return $this->errorResponse(
                    'No se encontraron contenidos para el tipo y categoría especificados',
                    404
                );
            }

            if ($count > 1 && $limit > 1) {
                $message = 'Se devuelven ' . $limit . ' contenidos aleatorios para el tipo ' . $type->name . ' y la categoría ' . $category->title . ' de ' . $count . ' contenidos totales.';
            } else if ($count > 1 && $limit == 1) {
                $message = 'Se devuelve ' . $limit . ' contenido aleatorio para el tipo ' . $type->name . ' y la categoría ' . $category->title . ' de ' . $count . ' contenidos totales.';
            } else if ($count == 1 && $limit > 1) {
                $message = 'Se devuelven ' . $limit . ' contenidos aleatorios para el tipo ' . $type->name . ' y la categoría ' . $category->title . ' de ' . $count . ' contenido total.';
            } else {
                $message = 'Se devuelve ' . $limit . ' contenido aleatorio para el tipo ' . $type->name . ' y la categoría ' . $category->title . ' de ' . $count . ' contenido total.';
            }

            return $this->collectionResponse(
                ContentResource::collection($contents->limit($limit)->get()),
                $message,
                [
                    'type' => $type->name,
                    'type_slug' => $type->slug,
                    'category' => $category->title,
                    'category_slug' => $category->slug,
                    'total_items' => $count,
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener contenidos del tipo especificado', 500);
        }
    }

    /**
     * Contenido en base a un grupo
     *
     * Contenido aleatorio que pertenecen al grupo recibido.
     *
     * @group 📚 Contenidos
     *
     * @urlParam group_slug string required El slug del grupo de contenido. Example: chistes-devs
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data array Lista con el contenido aleatorio solicitado
     * @responseField data[].title string Título del contenido
     * @responseField data[].content string Texto del contenido (chiste, adivinanza, etc.)
     * @responseField data[].urlImage string|null URL completa de la imagen asociada al contenido (null si no tiene imagen)
     * @responseField data[].uploader string Nick del usuario que subió el contenido (formato: @nick)
     * @responseField meta object Metadatos adicionales de la respuesta
     * @responseField meta.group string Título del grupo
     * @responseField meta.group_slug string Slug del grupo
     * @responseField meta.total_items integer Total de contenidos disponibles para este grupo
     *
     * @response 404 {
     *   "success": false,
     *   "message": "No se encontraron contenidos para el grupo especificado"
     * }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Error al obtener contenidos del tipo especificado"
     * }
     *
     * @param ContentRandomFromGroupRequest $request
     * @param Group $group Grupo por el que se filtra.
     * @return JsonResponse
     */
    public function getContentRandomFromGroup(ContentRandomFromGroupRequest $request, Group $group): JsonResponse
    {
        try {
            $limit = $request->getLimit();

            $contents = Content::select(['id', 'title', 'content', 'image', 'uploaded_by', 'user_id', 'group_id'])
                ->byGroup($group)
                ->random();

            $count = $contents->count();

            if (!$count) {
                return $this->errorResponse(
                    'No se encontraron contenidos para el grupo especificado',
                    404
                );
            }

            if ($count > 1 && $limit > 1) {
                $message = 'Se devuelven ' . $limit . ' contenidos aleatorios para el grupo ' . $group->title . ' de ' . $count . ' contenidos totales';
            } else if ($count > 1 && $limit == 1) {
                $message = 'Se devuelve ' . $limit . ' contenido aleatorio para el grupo ' . $group->title . ' de ' . $count . ' contenidos totales.';
            } else if ($count == 1 && $limit > 1) {
                $message = 'Se devuelven ' . $limit . ' contenidos aleatorios para el grupo ' . $group->title . ' de ' . $count . ' contenido total.';
            } else {
                $message = 'Se devuelve ' . $limit . ' contenido aleatorio para el grupo ' . $group->title . ' de ' . $count . ' contenido total.';
            }

            return $this->collectionResponse(
                ContentResource::collection($contents->limit($limit)->get()),
                $message,
                [
                    'group' => $group->title,
                    'group_slug' => $group->slug,
                    'total_items' => $count,
                ]
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error al obtener contenidos del grupo especificado', 500);
        }
    }

    /**
     * Envía una sugerencia de chiste a la plataforma
     *
     * Se permiten máximo 10 sugerencias en 1 minuto.
     *
     * @group 💡 Sugerencias
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     *
     * @response 201 {
     *     "success": true,
     *     "data": null,
     *     "message": "La sugerencia ha sido enviada correctamente."
     *   }
     *
     * @response 500 {
     *   "success": false,
     *   "message": "Error al añadir sugerencia, si persiste contacta con el administrador"
     * }
     *
     * @param SendSuggestionRequest $request
     * @return JsonResponse
     */
    public function sendSuggestion(SendSuggestionRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $suggestion = Suggestion::create(array_merge([
                'type_id' => $request->type_id,
                'ip_address' => $request->ip_address,
                'user_agent' => $request->user_agent,
            ], $data)
            );

            if ($suggestion) {
                return $this->successResponse(
                    [
                        'title' => $suggestion->title,
                        'content' => $suggestion->content,
                        'type' => $suggestion->type?->name,
                        'nick' => $suggestion->nick,
                    ],
                    'La sugerencia ha sido enviada correctamente.',
                    201,
                );
            }

            return $this->errorResponse('Error al añadir sugerencia, si persiste contacta con el administrador', 500);

        } catch (\Exception $e) {
            return $this->errorResponse('Error al añadir sugerencia, si persiste contacta con el administrador', 500);
        }
    }


    /**
     * Envía un reporte sobre contenido de la plataforma
     *
     * Se permiten máximo 10 reportes en 1 minuto por usuario.
     *
     * @group 🚨 Reportes
     *
     * @bodyParam content_id integer required ID del contenido a reportar. Example: 123
     * @bodyParam title string required Título del reporte (máximo 255 caracteres). Example: Contenido inapropiado
     * @bodyParam type string optional Tipo de reporte. Example: inappropriate_content
     * @bodyParam description string optional Descripción detallada del reporte (máximo 1024 caracteres). Example: Este contenido contiene lenguaje ofensivo
     * @bodyParam additional_info string optional Información adicional sobre el reporte (máximo 1024 caracteres). Example: Reportado por múltiples usuarios
     * @bodyParam reporter_name string optional Nombre del reporter (si difiere del usuario autenticado). Example: Juan Pérez
     * @bodyParam reporter_email string optional Email del reporter (si difiere del usuario autenticado). Example: juan@example.com
     *
     * @responseField success boolean Indica si la operación fue exitosa
     * @responseField message string Mensaje descriptivo de la operación
     * @responseField data object Datos del reporte creado
     *
     * @response 201 {
     *     "success": true,
     *     "data": {
     *         "id": 1,
     *         "title": "Contenido inapropiado",
     *         "type": "inappropriate_content",
     *         "status": "pending",
     *         "content_id": 123,
     *         "reporter_name": "Juan Pérez"
     *     },
     *     "message": "El reporte ha sido enviado correctamente y será revisado por nuestro equipo."
     * }
     *
     * @response 422 {
     *     "success": false,
     *     "message": "Error de validación",
     *     "errors": {
     *         "content_id": ["El ID del contenido es requerido."],
     *         "title": ["El título del reporte es requerido."]
     *     }
     * }
     *
     * @response 500 {
     *     "success": false,
     *     "message": "Error al enviar el reporte, si persiste contacta con el administrador"
     * }
     *
     * @param SendReportRequest $request
     * @return JsonResponse
     */
    public function sendReport(SendReportRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $report = Report::create([
                'user_id' => $data['user_id'],
                'reporter_name' => $data['reporter_name'] ?? null,
                'reporter_email' => $data['reporter_email'] ?? null,
                'reporter_ip' => $data['reporter_ip'],
                'reportable_type' => $data['reportable_type'],
                'reportable_id' => $data['reportable_id'],
                'title' => $data['title'],
                'type' => $data['type'] ?? 'other',
                'description' => $data['description'] ?? null,
                'additional_info' => $data['additional_info'] ?? null,
                'status' => 'pending',
                'priority' => 'medium',
                'assigned_to' => $data['assigned_to'],
            ]);

            if ($report) {
                return $this->successResponse(
                    [
                        'id' => $report->id,
                        'title' => $report->title,
                        'type' => $report->type,
                        'status' => $report->status,
                        'content_id' => $report->reportable_id,
                        'reporter_name' => $report->reporter_name,
                        'created_at' => $report->created_at->format('Y-m-d H:i:s'),
                    ],
                    'El reporte ha sido enviado correctamente y será revisado por nuestro equipo.',
                    201
                );
            }

            return $this->errorResponse('Error al enviar el reporte, si persiste contacta con el administrador', 500);

        } catch (\Exception $e) {
            \Log::error('Error al crear reporte: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'request_data' => $request->all(),
                'exception' => $e->getTraceAsString()
            ]);

            return $this->errorResponse('Error al enviar el reporte, si persiste contacta con el administrador', 500);
        }
    }

}

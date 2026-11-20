<?php

namespace App\Services;

use App\Models\CollaboratorProject;
use App\Models\Group;
use App\Models\Type;
use Illuminate\Support\Facades\Cache;

/**
 * Servicio centralizado para invalidar las claves de caché públicas y del panel
 * cuando se crean, actualizan, eliminan o restauran registros.
 */
class CacheInvalidationService
{
    public const ADMIN_CONTENT_OVERVIEW_KEY = 'admin_content_overview_stats';

    /**
     * Genera la clave de caché para la ficha de proyecto de un colaborador.
     */
    public static function collaboratorShowProjectKey(int $collaboratorId, int $projectId): string
    {
        return 'page_collaborator_' . $collaboratorId . '_show_project_' . $projectId;
    }

    /**
     * Invalida las cachés dependientes de contenidos aprobados.
     */
    public static function forContent(): void
    {
        Cache::forget('home_contents');
        Cache::forget('types_index');
        Cache::forget('groups_index');
        Cache::forget('categories_index');
        Cache::forget('contents_count');
        Cache::forget('users_active_total');
        Cache::forget('users_more_active_10');
        Cache::forget('users_more_active_20');
        Cache::forget(self::ADMIN_CONTENT_OVERVIEW_KEY);

        Type::withTrashed()
            ->pluck('id')
            ->each(static fn (int $typeId) => Cache::forget('groups_index_by_type_' . $typeId));
    }

    /**
     * Invalida los contadores de sugerencias pendientes y aceptadas.
     */
    public static function forSuggestion(): void
    {
        Cache::forget('suggestions_pending_count');
        Cache::forget('suggestions_accepted_count');
    }

    /**
     * Invalida las cachés asociadas a tipos de contenido.
     */
    public static function forType(Type $type): void
    {
        Cache::forget('types_index');
        Cache::forget('groups_index');
        Cache::forget('groups_index_by_type_' . $type->id);
        Cache::forget('types_and_groups_and_categories_count');
        Cache::forget(self::ADMIN_CONTENT_OVERVIEW_KEY);
    }

    /**
     * Invalida las cachés asociadas a grupos de contenido.
     */
    public static function forGroup(Group $group): void
    {
        Cache::forget('groups_index');
        Cache::forget('types_index');
        Cache::forget('home_contents');
        Cache::forget('types_and_groups_and_categories_count');
        Cache::forget(self::ADMIN_CONTENT_OVERVIEW_KEY);

        if ($group->type_id) {
            Cache::forget('groups_index_by_type_' . $group->type_id);
        }

        $originalTypeId = $group->getOriginal('type_id');
        if ($originalTypeId && (int) $originalTypeId !== (int) $group->type_id) {
            Cache::forget('groups_index_by_type_' . $originalTypeId);
        }
    }

    /**
     * Invalida las cachés asociadas a categorías.
     */
    public static function forCategory(): void
    {
        Cache::forget('categories_index');
        Cache::forget('types_and_groups_and_categories_count');
    }

    /**
     * Invalida el listado público de páginas del CMS.
     */
    public static function forPage(): void
    {
        Cache::forget('pages_index');
    }

    /**
     * Invalida las cachés públicas de colaboradores y sus proyectos.
     */
    public static function forCollaborator(int $collaboratorId): void
    {
        Cache::forget('page_collaborators_index');
        Cache::forget('page_collaborator_projects_count_' . $collaboratorId);
        Cache::forget('page_collaborator_' . $collaboratorId . '_projects');

        CollaboratorProject::where('collaborator_id', $collaboratorId)
            ->pluck('id')
            ->each(static fn (int $projectId) => Cache::forget(
                self::collaboratorShowProjectKey($collaboratorId, $projectId)
            ));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Content;
use App\Models\Group;
use App\Models\Type;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Controlador ContentController
 *
 * Gestiona la visualización de los contenidos (chistes, adivinanzas) clasificados por grupos, tipos y categorías en el frontend.
 */
class ContentController extends Controller
{
    /**
     * Listado de tipos
     *
     * @return View
     */
    public function typesIndex(): View
    {
        $types = Cache::remember('types_index', 60 * 60, function () {
            return Type::select('id', 'name', 'slug', 'description', 'image')
                ->withCount('groups')
                ->addSelect([
                    'last_content_date' => Content::selectRaw('MAX(contents.created_at)')
                        ->join('groups', 'groups.id', '=', 'contents.group_id')
                        ->whereColumn('groups.type_id', 'types.id'),
                ])
                ->orderBy('name')
                ->get();
        });

        return view('contents.types.index')->with([
            'types' => $types,
        ]);
    }

    /**
     * Método groupsIndex.
     *
     * @return View
     */
    public function groupsIndex(): View
    {
        $groups = Cache::remember('groups_index', 60 * 60, function () {
            return Group::select(['id', 'title', 'slug', 'image', 'description'])
                ->whereNotIn('id', [4, 14])
                ->hasAvailableContent()
                ->withCount(['contents' => function ($query) {
                    $query->whereNotIn('group_id', [4, 14])
                        ->where('is_adult', false);
                }])
                ->withMax(['contents as last_content_date' => function ($query) {
                    $query->whereNotIn('group_id', [4, 14])
                        ->where('is_adult', false);
                }], 'created_at')
                ->orderBy('contents_count', 'desc')
                ->get();
        });

        return view('contents.groups.index')->with([
            'groups' => $groups,
        ]);
    }

    /**
     * Listado de grupos dentro de un tipo
     *
     * @param Type $type
     * @return View
     */
    public function gruposByTypeIndex(Type $type): View
    {
        if (!$type?->id) {
            abort(404);
        }

        $groups = Cache::remember('groups_index_by_type_' . $type->id, 60 * 60, function () use ($type) {
            return $type->groups()
                ->select(['id', 'title', 'slug', 'image', 'description'])
                ->whereNotIn('groups.id', [4, 14])
                ->hasAvailableContent()
                ->withCount(['contents' => function ($query) {
                    $query->whereNotIn('group_id', [4, 14])
                        ->where('is_adult', false);
                }])
                ->withMax(['contents as last_content_date' => function ($query) {
                    $query->whereNotIn('group_id', [4, 14])
                        ->where('is_adult', false);
                }], 'created_at')
                ->orderBy('contents_count', 'desc')
                ->get();
        });

        return view('contents.groups.index')->with([
            'groups' => $groups,
            'type' => $type,
        ]);
    }

    /**
     * Listados de categorías
     *
     * @return View
     */
    public function categoriesIndex(): View
    {
        $categories = Cache::remember('categories_index', 60 * 60, function () {
            return Category::select(['id', 'title', 'slug', 'description', 'image'])
                ->hasAvailableContent()
                ->withCount(['contents' => function ($query) {
                    $query->whereNotIn('group_id', [4, 14])
                        ->where('is_adult', false);
                }])
                ->orderBy('title')
                ->get();
        });

        return view('contents.categories.index')->with([
            'categories' => $categories,
        ]);
    }

    /**
     * Devuelve contenido aleatorio en base a un grupo recibido
     *
     * @param Group $group
     * @return View
     */
    public function contentRandomFromGroup(Group $group): View
    {
        $contents = $group->contents()
            ->with([
                'group.type',
                'options' => fn ($q) => $q->orderBy('order'),
                'user:id,nick',
            ])
            ->whereNotIn('group_id', [4, 14])
            ->where('is_adult', false)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        if ($contents->isEmpty()) {
            abort(404);
        }

        return view('contents.groups.show')->with([
            'group' => $group,
            'contents' => $contents,
        ]);
    }

    /**
     * Devuelve contenido aleatorio en base a una categoría recibida
     *
     * @param Category $category
     * @return View
     */
    public function contentRandomFromcategory(Category $category): View
    {
        $contents = $category->contents()
            ->with([
                'group.type',
                'options' => fn ($q) => $q->orderBy('order'),
                'user:id,nick',
            ])
            ->whereNotIn('group_id', [4, 14])
            ->where('is_adult', false)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        if ($contents->isEmpty()) {
            abort(404);
        }

        return view('contents.categories.show')->with([
            'category' => $category,
            'contents' => $contents,
        ]);
    }
}

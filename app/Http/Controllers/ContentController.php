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
     */
    public function groupsIndex(): View
    {
        $groups = Cache::remember('groups_index', 60 * 60, function () {
            return Group::select(['id', 'title', 'slug', 'image', 'description'])
                ->publicSafe()
                ->hasAvailableContent()
                ->withCount(['contents' => fn ($query) => $query->publicSafe()])
                ->withMax(['contents as last_content_date' => fn ($query) => $query->publicSafe()], 'created_at')
                ->orderBy('contents_count', 'desc')
                ->get();
        });

        return view('contents.groups.index')->with([
            'groups' => $groups,
        ]);
    }

    /**
     * Listado de grupos dentro de un tipo
     */
    public function gruposByTypeIndex(Type $type): View
    {
        if (! $type->exists) {
            abort(404);
        }

        $groups = Cache::remember('groups_index_by_type_'.$type->id, 60 * 60, function () use ($type) {
            return $type->groups()
                ->select(['id', 'title', 'slug', 'image', 'description'])
                ->publicSafe()
                ->hasAvailableContent()
                ->withCount(['contents' => fn ($query) => $query->publicSafe()])
                ->withMax(['contents as last_content_date' => fn ($query) => $query->publicSafe()], 'created_at')
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
     */
    public function categoriesIndex(): View
    {
        $categories = Cache::remember('categories_index', 60 * 60, function () {
            return Category::select(['id', 'title', 'slug', 'description', 'image'])
                ->hasAvailableContent()
                ->withCount(['contents' => fn ($query) => $query->publicSafe()])
                ->orderBy('title')
                ->get();
        });

        return view('contents.categories.index')->with([
            'categories' => $categories,
        ]);
    }

    /**
     * Devuelve contenido aleatorio en base a un grupo recibido
     */
    public function contentRandomFromGroup(Group $group): View
    {
        $totalContents = $group->contents()
            ->publicSafe()
            ->count();

        if ($totalContents === 0) {
            abort(404);
        }

        $contents = $group->contents()
            ->with([
                'group.type',
                'options' => fn ($q) => $q->orderBy('order'),
                'user:id,nick',
            ])
            ->publicSafe()
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('contents.groups.show')->with([
            'group' => $group,
            'contents' => $contents,
            'hasMoreRandomContents' => $totalContents > 3,
        ]);
    }

    /**
     * Devuelve contenido aleatorio en base a una categoría recibida
     */
    public function contentRandomFromcategory(Category $category): View
    {
        $totalContents = $category->contents()
            ->publicSafe()
            ->count();

        if ($totalContents === 0) {
            abort(404);
        }

        $contents = $category->contents()
            ->with([
                'group.type',
                'options' => fn ($q) => $q->orderBy('order'),
                'user:id,nick',
            ])
            ->publicSafe()
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('contents.categories.show')->with([
            'category' => $category,
            'contents' => $contents,
            'hasMoreRandomContents' => $totalContents > 3,
        ]);
    }
}

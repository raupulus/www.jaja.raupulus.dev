<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Group;
use App\Models\Type;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ContentController extends Controller
{
    /**
     * Listado de tipos
     *
     * @return View
     */
    public function typesIndex(): View
    {
        $types = Cache::Remember('types_index', 60 * 60, function () {
            return Type::select('id', 'name', 'slug', 'description', 'image')
                ->withCount('groups')
                ->with(['contents' => function ($query) {
                    $query->select(['contents.created_at'])->latest('contents.created_at')->limit(1);
                }])
                ->orderBy('name')
                ->get();
        });

        return view('contents.types.index')->with([
            'types' => $types,
        ]);
    }

    public function groupsIndex(): View
    {
        $groups = Cache::remember('groups_index', 60 * 60, function () {
            return Group::select(['id', 'title', 'slug', 'image', 'description'])
                ->withCount('contents')
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
                ->withCount('contents')
                ->orderBy('contents_count', 'desc')
                ->get();
        });

        return view('contents.groups.index')->with([
            'groups' => $groups,
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
                ->withCount('contents')
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
            ->whereNotIn('group_id', [4, 14])
            ->whereNot('is_adult')
            ->inRandomOrder()
            ->limit(3)
            ->get();

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
            ->whereNotIn('group_id', [4, 14])
            ->whereNot('is_adult')
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('contents.categories.show')->with([
            'category' => $category,
            'contents' => $contents,
        ]);
    }
}

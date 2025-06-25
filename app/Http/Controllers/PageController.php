<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Listado de todas las páginas
     *
     * @return View
     */
    public function index(): View
    {
        $pages = Cache::remember('pages_index', 600, function () {
            return Page::select(['title', 'slug', 'excerpt', 'image', 'keywords'])
                ->where('status', 'published')
                ->get();
        });

        return view('pages.index', [
            'pages' => $pages,
        ]);
    }

    /**
     * Muestra una página individual
     *
     * @param Page $page
     * @return View
     */
    public function show(Page $page): View
    {
        if (!$page->id || ($page->status !== 'published')) {
            abort(404);
        }

        return view('pages.show')->with([
            'page' => $page,
        ]);
    }
}

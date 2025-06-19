<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{

    public function index(): View
    {
        return view('pages.index', [
            'pages' => Page::select(['title', 'slug', 'excerpt', 'image', 'keywords'])
                ->where('status', 'published')
                ->get(),
        ]);
    }
    public function show(Page $page): View
    {
        if (!$page || ($page->status !== 'published')) {
            abort(404);
        }

        return view('pages.show')->with([
            'page' => $page,
        ]);
    }
}

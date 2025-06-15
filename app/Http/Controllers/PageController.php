<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = new Page();

        return view('pages.show')->with([
            'page' => $page,
        ]);
    }
}

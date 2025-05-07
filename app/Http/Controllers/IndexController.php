<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class IndexController extends Controller
{
    /**
     * Página principal.
     *
     * @return View
     */
    public function index(): View
    {
        return view('home');
    }
}

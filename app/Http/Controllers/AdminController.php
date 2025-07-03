<?php

namespace App\Http\Controllers;

use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminController extends Controller
{


    /**
     * Acción para generar el sitemap de nuevo.
     *
     * @return RedirectResponse
     */
    public function generateSitemap(): RedirectResponse
    {
        Artisan::call('sitemap:generate');


        Notification::make()->title('Sitemap generado')->body('Sitemap generado correctamente')->success()->send();

        return redirect()->back();
    }

    /**
     * Acción para generar las estadísticas de nuevo.
     * @return RedirectResponse
     */
    public function generateStats(): RedirectResponse
    {
        Artisan::call('stats:update');

        Notification::make()->title('Stats generados')->body('Stats generados correctamente')->success()->send();

        return redirect()->back();
    }
}

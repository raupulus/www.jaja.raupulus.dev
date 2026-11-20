<?php

namespace App\Http\Controllers;

use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command;

/**
 * Controlador AdminController
 *
 * Gestiona la lógica principal y acciones administrativas del panel.
 */
class AdminController extends Controller
{
    /**
     * Acción para generar el sitemap de nuevo.
     */
    public function generateSitemap(): RedirectResponse
    {
        try {
            $exitCode = Artisan::call('sitemap:generate');

            if ($exitCode !== Command::SUCCESS) {
                Log::error('AdminController failed to execute sitemap:generate', [
                    'exit_code' => $exitCode,
                    'output' => Artisan::output(),
                ]);

                Notification::make()
                    ->title('Error al generar Sitemap')
                    ->body('No se pudo completar la generación del sitemap.')
                    ->danger()
                    ->send();

                return redirect()->back();
            }

            Notification::make()
                ->title('Sitemap generado')
                ->body('Sitemap generado correctamente')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::error('AdminController exception while generating sitemap', [
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Error al generar Sitemap')
                ->body('No se pudo completar la generación del sitemap.')
                ->danger()
                ->send();
        }

        return redirect()->back();
    }

    /**
     * Acción para generar las estadísticas de nuevo.
     */
    public function generateStats(): RedirectResponse
    {
        try {
            $exitCode = Artisan::call('stats:update');

            if ($exitCode !== Command::SUCCESS) {
                Log::error('AdminController failed to execute stats:update', [
                    'exit_code' => $exitCode,
                    'output' => Artisan::output(),
                ]);

                Notification::make()
                    ->title('Error al actualizar estadísticas')
                    ->body('No se pudieron recalcular las estadísticas.')
                    ->danger()
                    ->send();

                return redirect()->back();
            }

            Notification::make()
                ->title('Stats generados')
                ->body('Stats generados correctamente')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Log::error('AdminController exception while updating stats', [
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Error al actualizar estadísticas')
                ->body('No se pudieron recalcular las estadísticas.')
                ->danger()
                ->send();
        }

        return redirect()->back();
    }
}

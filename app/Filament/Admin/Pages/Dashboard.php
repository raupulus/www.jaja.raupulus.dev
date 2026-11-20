<?php

namespace App\Filament\Admin\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Command\Command;

/**
 * Dashboard principal de administración con acciones confirmadas de mantenimiento.
 */
class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_sitemap')
                ->label('Generar Sitemap')
                ->icon('heroicon-o-globe-alt')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Confirmar generación de Sitemap')
                ->modalDescription('Se regenerará el archivo sitemap.xml público con los contenidos y taxonomías vigentes.')
                ->modalSubmitActionLabel('Generar Sitemap')
                ->action(function (): void {
                    try {
                        $exitCode = Artisan::call('sitemap:generate');

                        if ($exitCode !== Command::SUCCESS) {
                            Log::error('Filament Dashboard failed to execute sitemap:generate', [
                                'exit_code' => $exitCode,
                                'output' => Artisan::output(),
                            ]);

                            Notification::make()
                                ->title('Error al generar el Sitemap')
                                ->body('El comando sitemap:generate devolvió un fallo. Revisa los registros del sistema.')
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Sitemap generado')
                            ->body('El archivo sitemap.xml se ha regenerado correctamente.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Log::error('Filament Dashboard exception while generating sitemap', [
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->title('Error al generar el Sitemap')
                            ->body('No se pudo completar la generación del sitemap.')
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('update_stats')
                ->label('Actualizar Estadísticas')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Confirmar actualización de estadísticas')
                ->modalDescription('Se recalcularán las estadísticas de usuarios y contenidos en caché.')
                ->modalSubmitActionLabel('Actualizar')
                ->action(function (): void {
                    try {
                        $exitCode = Artisan::call('stats:update');

                        if ($exitCode !== Command::SUCCESS) {
                            Log::error('Filament Dashboard failed to execute stats:update', [
                                'exit_code' => $exitCode,
                                'output' => Artisan::output(),
                            ]);

                            Notification::make()
                                ->title('Error al actualizar estadísticas')
                                ->body('El comando stats:update devolvió un fallo. Revisa los registros del sistema.')
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Estadísticas actualizadas')
                            ->body('Las estadísticas de la plataforma se han recalculado correctamente.')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Log::error('Filament Dashboard exception while updating stats', [
                            'error' => $e->getMessage(),
                        ]);

                        Notification::make()
                            ->title('Error al actualizar estadísticas')
                            ->body('No se pudo completar la actualización de estadísticas.')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}

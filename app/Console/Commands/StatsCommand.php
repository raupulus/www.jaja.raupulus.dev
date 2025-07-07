<?php

namespace App\Console\Commands;

use App\Helpers\StatsHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stats:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza las estadísticas de la plataforma';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando actualización de estadísticas');

        try {
            Cache::forget('suggestions_pending_count');
            StatsHelper::getSuggestionsPending();
            $this->info('✓ Caché de sugerencias pendientes actualizado');

            Cache::forget('suggestions_accepted_count');
            StatsHelper::getSuggestionsAccepted();
            $this->info('✓ Caché de sugerencias aceptadas actualizado');

            Cache::forget('contents_count');
            StatsHelper::getContentsTotal();
            $this->info('✓ Caché de contenidos totales actualizado');

            Cache::forget('users_active_total');
            StatsHelper::getUsersActiveTotal();
            $this->info('✓ Caché de total de usuarios activos actualizado');

            Cache::forget('users_more_active_10');
            StatsHelper::getUsersMoreActive(10);

            Cache::forget('users_more_active_20');
            StatsHelper::getUsersMoreActive(20);
            $this->info('✓ Caché de usuarios más activos actualizado');

            Cache::forget('types_and_groups_and_categories_count');
            StatsHelper::typesAndGroupsAndCategoriesCount();
            $this->info('✓ Caché del contador de grupos, tipos y categorías actualizado');

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error durante la actualización de estadísticas: ' . $e->getMessage());

            Log::error('StatsCommand: Error durante la actualización', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            return self::FAILURE;
        }
    }
}

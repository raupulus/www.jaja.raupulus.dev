<?php

namespace App\Console\Commands;

use App\Helpers\PublishToSocialHelper;
use App\Models\Content;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PublishContentToSocialCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:publish-social';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publica el contenido que lleva más tiempo sin publicar en redes sociales';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando contenido para publicar en redes sociales...');

        ## Busco el contenido con menor fecha o null para el campo last_social_published del contenido
        $content = Content::with('categories')
            ->whereNotIn('group_id', [4, 14])
            ->where('is_adult', false)
            ->orderByRaw('last_social_published IS NULL DESC')
            ->orderBy('last_social_published', 'asc')
            ->first();

        if (!$content) {
            $this->warn('No se encontró contenido para publicar.');
            return self::SUCCESS;
        }

        $this->info("Contenido encontrado: {$content->title}");

        try {
            PublishToSocialHelper::publishContent($content);

            $this->info('Contenido publicado exitosamente.');
        } catch (\Exception $e) {
            $this->error('Error al publicar contenido: ' . $e->getMessage());

            Log::error('Error al publicar contenido en redes sociales', [
                'content_id' => $content->id,
                'error' => $e->getMessage(),
            ]);

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}

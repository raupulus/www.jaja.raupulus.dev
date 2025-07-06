<?php

namespace App\Console\Commands;

use App\Models\Content;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
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
    protected $description = 'Publica el contenido más antiguo sin publicar en redes sociales';

    /**
     * URL de la API donde se enviará el contenido
     */
    private const API_URL = 'http://localhost:8080/publish';

    /**
     * Hashtags predefinidos que siempre se envían
     */
    private const DEFAULT_HASHTAGS = ['Chiste', 'Humor', 'Meme'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando contenido para publicar en redes sociales...');

        // Buscar el contenido con menor fecha o null en last_social_published
        $content = Content::with('categories')
            ->whereNotIn('group_id', [4, 14])
            ->orderByRaw('last_social_published IS NULL DESC')
            ->orderBy('last_social_published', 'asc')
            ->first();

        if (!$content) {
            $this->warn('No se encontró contenido para publicar.');
            return self::SUCCESS;
        }

        $this->info("Contenido encontrado: {$content->title}");

        try {
            $this->publishContent($content);
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

    /**
     * Publica el contenido en la API externa
     */
    private function publishContent(Content $content): void
    {
        $payload = [
            'content' => $content->content,
            'title' => $content->title,
            'project' => 'jajaproject',
        ];

        ## Preparo hashtags
        $hashtags = collect(self::DEFAULT_HASHTAGS);

        ## Agrego categorías del contenido (excluyendo "General")
        if ($content->categories->isNotEmpty()) {
            $categoryHashtags = $content->categories
                ->pluck('title')
                ->filter(function ($title) {
                    return !empty($title) && strtolower($title) !== 'general';
                })
                ->values()
                ->toArray();

            $hashtags = $hashtags->merge($categoryHashtags);
        }

        ## Elimino duplicados y convierto en array
        $payload['hashtags'] = $hashtags->unique()->values()->toArray();

        ## Agrego imágenes si existe
        if ($content->image) {
            $payload['images'] = [$content->urlImage];
        }

        /*
        Log::info('Enviando payload a la API', [
            'content_id' => $content->id,
            'payload' => $payload,
            'url' => self::API_URL,
        ]);
        */

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(self::API_URL, $payload);

        /*
        Log::info('Respuesta de la API', [
            'content_id' => $content->id,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
        */

        ## Verifico si la respuesta HTTP fue exitosa
        if (!$response->successful()) {
            throw new \Exception("Error HTTP en la API: {$response->status()} - {$response->body()}");
        }

        ## Verifico si la respuesta JSON contiene success: true
        $responseData = $response->json();
        if (!isset($responseData['success']) || $responseData['success'] !== true) {
            throw new \Exception("La API devolvió success: false - " . ($responseData['message'] ?? $responseData['error'] ?? 'Sin mensaje'));
        }

        ## Actualizo la fecha de publicación en redes sociales solo si fue exitoso
        $content->update([
            'last_social_published' => now(),
        ]);

        /*
        Log::info('Contenido publicado exitosamente en redes sociales', [
            'content_id' => $content->id,
            'title' => $content->title,
            'response_status' => $response->status(),
            'api_response' => $responseData,
        ]);
        */
    }
}

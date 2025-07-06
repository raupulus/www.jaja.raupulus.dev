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
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Buscando contenido para publicar en redes sociales...');

        // Buscar el contenido con menor fecha o null en last_social_published
        $content = Content::with('categories')
            ->orderByRaw('last_social_published IS NULL DESC')
            ->orderBy('last_social_published', 'asc')
            ->first();

        if (!$content) {
            $this->warn('No se encontró contenido para publicar.');
            return Command::SUCCESS;
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
            return Command::FAILURE;
        }

        return Command::SUCCESS;
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

        // Agregar hashtags si existen categorías (usando 'title' en lugar de 'name')
        if ($content->categories->isNotEmpty()) {
            $hashtags = $content->categories->pluck('title')->filter()->toArray();
            if (!empty($hashtags)) {
                $payload['hashtags'] = $hashtags;
            }
        }

        // Agregar imágenes si existe (campo plural "images")
        if ($content->image) {
            $payload['images'] = [$content->urlImage];
        }

        // Log del payload para debugging
        Log::info('Enviando payload a la API', [
            'content_id' => $content->id,
            'payload' => $payload,
            'url' => self::API_URL,
        ]);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post(self::API_URL, $payload);

        // Log de la respuesta completa para debugging
        Log::info('Respuesta de la API', [
            'content_id' => $content->id,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        // Verificar si la respuesta HTTP fue exitosa
        if (!$response->successful()) {
            throw new \Exception("Error HTTP en la API: {$response->status()} - {$response->body()}");
        }

        // Verificar si la respuesta JSON contiene success: true
        $responseData = $response->json();
        if (!isset($responseData['success']) || $responseData['success'] !== true) {
            throw new \Exception("La API devolvió success: false - " . ($responseData['message'] ?? $responseData['error'] ?? 'Sin mensaje'));
        }

        // Actualizar la fecha de publicación en redes sociales solo si fue exitoso
        $content->update([
            'last_social_published' => now(),
        ]);

        Log::info('Contenido publicado exitosamente en redes sociales', [
            'content_id' => $content->id,
            'title' => $content->title,
            'response_status' => $response->status(),
            'api_response' => $responseData,
        ]);
    }
}

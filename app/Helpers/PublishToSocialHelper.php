<?php

namespace App\Helpers;

use App\Models\Content;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PublishToSocialHelper
{
    /**
     * Hashtags predefinidos que siempre se envían
     */
    private const DEFAULT_HASHTAGS = ['Chiste', 'Humor', 'Meme'];

    /**
     * Publica un modelo Content en redes sociales
     *
     * @param  Content  $content  Modelo Content a publicar
     * @param  bool  $updateTimestamp  Indica si debe actualizar el campo last_social_published
     *
     * @throws \Exception
     */
    public static function publishContent(Content $content, bool $updateTimestamp = true): bool
    {
        // # Verifico si el contenido es apto para publicar
        if ($content->is_adult) {
            throw new \Exception('Este contenido no es apto para publicar en redes sociales.');
        }

        // # Cargo relaciones necesarias
        $content->load('categories');

        // # Preparo tags de categorías
        $tags = [];
        if ($content->categories->isNotEmpty()) {
            $tags = $content->categories->pluck('title')->toArray();
        }

        // # Preparar imágenes si tuviese
        $images = [];
        if ($content->image) {
            $images = [$content->urlImage];
        }

        // # Publica usando el método base
        self::publish($content->title, $content->content, $tags, $images);

        // # Actualizo timestamp si se solicita
        if ($updateTimestamp) {
            $content->update([
                'last_social_published' => now(),
            ]);
        }

        return true;
    }

    /**
     * Publica contenido en redes sociales
     *
     * @param  string  $title  Título del contenido
     * @param  string  $content  Contenido a publicar
     * @param  array  $tags  Array de strings con tags adicionales
     * @param  array  $images  Array de URLs completas de imágenes
     *
     * @throws \Exception
     */
    public static function publish(string $title, string $content, array $tags = [], array $images = []): bool
    {
        $payload = [
            'content' => $content,
            'title' => $title,
            'project' => 'jajaproject',
        ];

        // # Preparo hashtags
        $hashtags = collect(self::DEFAULT_HASHTAGS);

        // # Agrego tags adicionales (excluyendo "General")
        if (! empty($tags)) {
            $filteredTags = collect($tags)
                ->filter(function ($tag) {
                    return ! empty($tag) && strtolower($tag) !== 'general';
                })
                ->values()
                ->toArray();

            $hashtags = $hashtags->merge($filteredTags);
        }

        // # Elimino duplicados y convierto en array
        $payload['hashtags'] = $hashtags->unique()->values()->toArray();

        // # Agrego imágenes si existen
        if (! empty($images)) {
            $payload['images'] = $images;
        }

        $url = (string) config('services.social_publisher.url', 'http://localhost:8080/publish');
        $connectTimeout = max(1, (int) config('services.social_publisher.connect_timeout', 5));
        $timeout = max(1, (int) config('services.social_publisher.timeout', 15));

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])
                ->connectTimeout($connectTimeout)
                ->timeout($timeout)
                ->post($url, $payload);
        } catch (ConnectionException $e) {
            Log::error('Social publisher HTTP connection failed', [
                'url' => $url,
                'title' => $title,
                'exception' => $e->getMessage(),
            ]);

            throw new \Exception('No se pudo conectar con el servicio de publicación social: '.$e->getMessage(), 0, $e);
        }

        // # Verifico si la respuesta HTTP fue exitosa
        if (! $response->successful()) {
            Log::error('Social publisher returned non-successful HTTP status', [
                'url' => $url,
                'status' => $response->status(),
                'title' => $title,
            ]);

            throw new \Exception("Error HTTP en la API: {$response->status()} - {$response->body()}");
        }

        // # Verifico si la respuesta JSON contiene success: true
        $responseData = $response->json();
        if (! isset($responseData['success']) || $responseData['success'] !== true) {
            $apiMessage = $responseData['message'] ?? $responseData['error'] ?? 'Sin mensaje';

            Log::error('Social publisher API returned failure payload', [
                'url' => $url,
                'title' => $title,
                'api_message' => $apiMessage,
            ]);

            throw new \Exception('La API devolvió success: false - '.$apiMessage);
        }

        return true;
    }
}

<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager;

/**
 * Acción para orientar, escalar, limpiar metadatos EXIF y convertir imágenes a WebP.
 */
class ConvertImageToWebp
{
    /**
     * Convierte la imagen recibida a WebP en almacenamiento público sin metadatos EXIF.
     *
     * @param  string  $path  Ruta absoluta del fichero temporal/origen
     * @param  string|null  $directory  Subdirectorio destino en el disco public
     * @param  int  $maxDimension  Dimensión máxima permitida en ancho o alto (px)
     * @param  int  $quality  Calidad de compresión WebP (1-100)
     * @return string Ruta relativa en el disco public o la ruta original si falla
     */
    public function __invoke(string $path, ?string $directory = null, int $maxDimension = 2560, int $quality = 85): string
    {
        try {
            if ($path === '' || ! file_exists($path)) {
                Log::warning('Invalid image path or file does not exist for WebP conversion', [
                    'path' => $path,
                ]);

                return $path;
            }

            $manager = new ImageManager(new Driver);
            $image = $manager->read($path);

            // Aplica orientación física EXIF y limita resolución máxima manteniendo proporción
            $image->orient()->scaleDown(width: $maxDimension, height: $maxDimension);

            $targetDirectory = trim($directory ?? 'general', '/');
            $targetDirectory = $targetDirectory !== '' ? $targetDirectory : 'general';

            $newFileName = $targetDirectory.'/'.time().'_'.Str::random(10).'.webp';

            // Re-codifica a WebP eliminando explícitamente metadatos EXIF/IPTC
            $encoded = $image->encode(new WebpEncoder(quality: $quality, strip: true));

            $saved = Storage::disk('public')->put($newFileName, (string) $encoded);

            if ($saved && Storage::disk('public')->exists($newFileName)) {
                return $newFileName;
            }

            Log::error('Failed to store converted WebP image', [
                'target' => $newFileName,
            ]);

            return $path;
        } catch (\Throwable $e) {
            Log::error('Error converting image to WebP', [
                'error' => $e->getMessage(),
            ]);

            return $path;
        }
    }
}

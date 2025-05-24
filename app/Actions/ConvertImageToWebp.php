<?php

namespace App\Actions;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ConvertImageToWebp
{
    public function __invoke(string $path): string
    {
        try {
            //\Log::info("Iniciando conversión. Path recibido: " . $path);

            if (empty($path) || !file_exists($path)) {
                \Log::warning("Path inválido o archivo no existe");
                return $path;
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($path);

            ## Nuevo nombre
            $newFileName = 'user-images/' . time() . '_' . Str::random(10) . '.webp';
            $newFullPath = Storage::disk('public')->path($newFileName);

            ## Existe el directorio=?
            if (!file_exists(dirname($newFullPath))) {
                mkdir(dirname($newFullPath), 0755, true);
            }

            ## Guarda directamente como WebP
            $image->encode(new WebpEncoder(90))->save($newFullPath);

            if (file_exists($newFullPath)) {
                //\Log::info("Archivo WebP creado exitosamente en: " . $newFullPath);
                return $newFileName;
            }

            \Log::error("No se pudo crear el archivo WebP");
            return $path;

        } catch (\Exception $e) {
            \Log::error('Error al convertir imagen: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return $path;
        }
    }
}

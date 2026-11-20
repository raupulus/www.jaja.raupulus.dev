<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * Trait para eliminar automáticamente imágenes físicas en Storage::disk('public')
 * al reemplazar el atributo o borrar definitivamente el modelo.
 */
trait DeletesStoredImages
{
    /**
     * Registra listeners Eloquent para limpiar imágenes obsoletas o eliminadas.
     */
    protected static function bootDeletesStoredImages(): void
    {
        static::updating(function ($model): void {
            foreach ($model->storedImageAttributes() as $attribute => $defaultPath) {
                if (! $model->isDirty($attribute)) {
                    continue;
                }

                $originalPath = $model->getOriginal($attribute);
                $newPath = $model->getAttribute($attribute);

                if (is_string($originalPath) && $originalPath !== $newPath) {
                    $model->deleteStoredImageFile($originalPath, $defaultPath);
                }
            }
        });

        $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive(static::class), true);

        if ($usesSoftDeletes) {
            static::forceDeleting(function ($model): void {
                foreach ($model->storedImageAttributes() as $attribute => $defaultPath) {
                    $model->deleteStoredImageFile($model->getAttribute($attribute), $defaultPath);
                }
            });
        } else {
            static::deleting(function ($model): void {
                foreach ($model->storedImageAttributes() as $attribute => $defaultPath) {
                    $model->deleteStoredImageFile($model->getAttribute($attribute), $defaultPath);
                }
            });
        }
    }

    /**
     * Mapa atributo => ruta por defecto que nunca debe borrarse.
     *
     * @return array<string, string|null>
     */
    protected function storedImageAttributes(): array
    {
        return ['image' => null];
    }

    /**
     * Elimina físicamente una imagen del disco público evitando rutas por defecto o inseguras.
     */
    public function deleteStoredImageFile(?string $path, ?string $defaultPath = null): bool
    {
        if (
            ! is_string($path)
            || $path === ''
            || ($defaultPath !== null && $path === $defaultPath)
            || str_starts_with($path, 'http://')
            || str_starts_with($path, 'https://')
            || str_contains($path, '..')
        ) {
            return false;
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($path)) {
            return false;
        }

        return $disk->delete($path);
    }
}

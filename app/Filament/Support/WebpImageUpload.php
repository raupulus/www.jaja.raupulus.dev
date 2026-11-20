<?php

namespace App\Filament\Support;

use App\Actions\ConvertImageToWebp;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Log;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Factoría compartida para campos FileUpload en Filament con conversión WebP y saneamiento EXIF.
 */
class WebpImageUpload
{
    public static function make(string $name, string $directory, string $label = 'Imagen'): FileUpload
    {
        return FileUpload::make($name)
            ->image()
            ->disk('public')
            ->directory($directory)
            ->visibility('public')
            ->imageEditor()
            ->label($label)
            ->default(null)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->maxSize(2048)
            ->afterStateUpdated(function (FileUpload $component, mixed $state) use ($directory): void {
                if (! $state instanceof TemporaryUploadedFile) {
                    return;
                }

                try {
                    $tempFile = $state->getRealPath();
                    $converter = new ConvertImageToWebp;
                    $newPath = $converter($tempFile, $directory);

                    if ($newPath && $newPath !== $tempFile) {
                        $component->state([$newPath]);
                    }
                } catch (\Throwable $e) {
                    Log::error('Failed to convert uploaded Filament image to WebP', [
                        'directory' => $directory,
                        'exception' => $e->getMessage(),
                    ]);
                }
            });
    }
}

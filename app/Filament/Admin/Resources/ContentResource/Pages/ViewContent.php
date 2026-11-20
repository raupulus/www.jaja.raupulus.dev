<?php

namespace App\Filament\Admin\Resources\ContentResource\Pages;

use App\Filament\Admin\Resources\ContentResource;
use App\Helpers\PublishToSocialHelper;
use App\Models\Content;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewContent extends ViewRecord
{
    protected static string $resource = ContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('publish_social')
                ->label('Publicar')
                ->icon('heroicon-o-share')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Confirmar publicación en redes sociales')
                ->modalDescription(function (): string {
                    $record = $this->getRecord();
                    $lastPublished = ($record instanceof Content && $record->last_social_published)
                        ? $record->last_social_published->diffForHumans()
                        : 'Nunca';

                    return "¿Estás seguro de que quieres publicar este contenido en redes sociales?\n\nÚltima publicación: {$lastPublished}";
                })

                ->modalSubmitActionLabel('Publicar')
                ->action(function (): void {
                    $record = $this->getRecord();
                    if (! $record instanceof Content) {
                        return;
                    }

                    try {
                        PublishToSocialHelper::publishContent($record);

                        Notification::make()
                            ->title('Contenido publicado')
                            ->body('El contenido se ha publicado exitosamente en redes sociales.')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Error al publicar')
                            ->body('Error: '.$e->getMessage())
                            ->danger()
                            ->send();
                    }
                })
                ->visible(function (): bool {
                    $record = $this->getRecord();

                    // # Solo muestro botón en contenido apto para publicar
                    return $record instanceof Content && $record->isPublicSafe();
                }),
        ];
    }
}

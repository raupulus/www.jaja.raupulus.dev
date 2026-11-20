<?php

namespace App\Filament\Admin\Resources\SuggestionResource\Pages;

use App\Filament\Admin\Resources\SuggestionResource;
use App\Models\Suggestion;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuggestion extends EditRecord
{
    protected static string $resource = SuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\Action::make('Aprobar')
                ->action(function (): void {
                    $record = $this->getRecord();
                    if ($record instanceof Suggestion) {
                        $record->approve();
                    }
                })
                ->color('success')
                ->icon('heroicon-o-check')
                ->requiresConfirmation()
                ->modalHeading('Confirmar aprobación de sugerencia')
                ->modalDescription('Se creará una entrada publicada en el módulo de contenidos con estos datos.')
                ->visible(function (): bool {
                    $record = $this->getRecord();

                    return $record instanceof Suggestion &&
                        $record->deleted_at === null &&
                        $record->approved_at === null;
                }),

        ];
    }
}

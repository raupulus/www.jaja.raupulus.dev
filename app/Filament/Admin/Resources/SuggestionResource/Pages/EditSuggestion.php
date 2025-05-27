<?php

namespace App\Filament\Admin\Resources\SuggestionResource\Pages;

use App\Filament\Admin\Resources\SuggestionResource;
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
                ->action(fn () => $this->record->approve())
                ->color('success')
                ->icon('heroicon-o-check'),
        ];
    }

}

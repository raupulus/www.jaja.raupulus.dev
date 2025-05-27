<?php

namespace App\Filament\Admin\Resources\SuggestionResource\Pages;

use App\Filament\Admin\Resources\SuggestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuggestions extends ListRecords
{
    protected static string $resource = SuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Panel\Resources\ContentResource\Pages;

use App\Filament\Panel\Resources\ContentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContents extends ListRecords
{
    protected static string $resource = ContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nuevo Contenido'),
        ];
    }
}

<?php

namespace App\Filament\Admin\Resources\CollaboratorResource\Pages;

use App\Filament\Admin\Resources\CollaboratorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCollaborators extends ListRecords
{
    protected static string $resource = CollaboratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

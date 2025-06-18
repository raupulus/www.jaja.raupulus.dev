<?php

namespace App\Filament\Admin\Resources\CollaboratorResource\Pages;

use App\Filament\Admin\Resources\CollaboratorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCollaborator extends EditRecord
{
    protected static string $resource = CollaboratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

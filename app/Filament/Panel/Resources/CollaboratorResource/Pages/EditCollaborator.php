<?php

namespace App\Filament\Panel\Resources\CollaboratorResource\Pages;

use App\Filament\Panel\Resources\CollaboratorResource;
use App\Models\Collaborator;
use Filament\Resources\Pages\EditRecord;

class EditCollaborator extends EditRecord
{
    protected static string $resource = CollaboratorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No incluyo DeleteAction para que los usuarios no puedan eliminar su perfil
        ];
    }

    protected function authorizeAccess(): void
    {
        parent::authorizeAccess();

        $record = $this->getRecord();

        if (! $record instanceof Collaborator || $record->user_id !== auth()->id()) {
            abort(403, 'No tienes permisos para acceder a este colaborador.');
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['user_id']);

        return $data;
    }
}

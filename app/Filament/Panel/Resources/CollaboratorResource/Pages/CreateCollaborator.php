<?php

namespace App\Filament\Panel\Resources\CollaboratorResource\Pages;

use App\Filament\Panel\Resources\CollaboratorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCollaborator extends CreateRecord
{
    protected static string $resource = CollaboratorResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        return $data;
    }

    protected function afterCreate(): void
    {
        // Redirigir a la página de edición después de crear
        $this->redirect($this->getResource()::getUrl('edit', ['record' => $this->getRecord()]));
    }
}

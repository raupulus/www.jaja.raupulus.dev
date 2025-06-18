<?php

namespace App\Filament\Panel\Resources\CollaboratorResource\Pages;

use App\Filament\Panel\Resources\CollaboratorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCollaborators extends ListRecords
{
    protected static string $resource = CollaboratorResource::class;

    public function mount(): void
    {
        ## Busco colaborador
        $collaborator = $this->getResource()::getEloquentQuery()->where('user_id', auth()->id())->first();

        if ($collaborator) {
            ## Si existe, redirijo a editar
            $this->redirect($this->getResource()::getUrl('edit', ['record' => $collaborator]));
        } else {
            ## Si no existe, redirijo a crear
            $this->redirect($this->getResource()::getUrl('create'));
        }
    }

    protected function getHeaderActions(): array
    {
        // Este método ya no se ejecutará porque siempre redirijo
        return [];
    }
}

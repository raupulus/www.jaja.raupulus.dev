<?php

namespace App\Filament\Panel\Resources\ContentResource\Pages;

use App\Filament\Panel\Resources\ContentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContent extends EditRecord
{
    protected static string $resource = ContentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['user_id']);

        return $data;
    }
}

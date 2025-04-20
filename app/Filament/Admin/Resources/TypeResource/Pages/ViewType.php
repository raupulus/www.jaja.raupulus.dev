<?php

namespace App\Filament\Admin\Resources\TypeResource\Pages;

use App\Filament\Admin\Resources\TypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewType extends ViewRecord
{
    protected static string $resource = TypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

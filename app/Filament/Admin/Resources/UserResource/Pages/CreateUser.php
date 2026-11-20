<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $roleId = (int) ($data['role_id'] ?? 2);
        unset($data['role_id']);

        $modelClass = $this->getModel();
        $record = new $modelClass($data);

        if (auth()->user()?->isAdmin && in_array($roleId, [1, 2], true)) {
            $record->role_id = $roleId;
        } else {
            $record->role_id = 2;
        }

        $record->save();

        return $record;
    }
}

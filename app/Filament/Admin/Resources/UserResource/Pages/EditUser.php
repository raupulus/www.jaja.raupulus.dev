<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use App\Filament\Admin\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        ## Si no se proporciona contraseña, eliminamos el campo para mantener la existente
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $roleId = isset($data['role_id']) ? (int) $data['role_id'] : (int) $record->role_id;
        unset($data['role_id']);

        $record->fill($data);

        if (auth()->user()?->isAdmin && in_array($roleId, [1, 2], true)) {
            if ((int) $record->role_id === 1 && $roleId !== 1) {
                $hasOtherAdmin = User::where('role_id', 1)
                    ->whereNull('deleted_at')
                    ->where('id', '!=', $record->id)
                    ->exists();

                if ($hasOtherAdmin) {
                    $record->role_id = $roleId;
                }
            } else {
                $record->role_id = $roleId;
            }
        }

        $record->save();

        return $record;
    }
}

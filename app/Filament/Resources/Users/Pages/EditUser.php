<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (User $record): bool => ! $record->is(auth()->user())),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $isAdmin = $data['is_admin'] ?? null;
        unset($data['is_admin']);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $record->fill($data);

        if ($isAdmin !== null && ! $record->is(auth()->user())) {
            $record->is_admin = (bool) $isAdmin;
        }

        $record->save();

        return $record;
    }
}

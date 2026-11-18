<?php

namespace App\Filament\Resources\VillageSettings\Pages;

use App\Filament\Resources\VillageSettings\VillageSettingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVillageSetting extends EditRecord
{
    protected static string $resource = VillageSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

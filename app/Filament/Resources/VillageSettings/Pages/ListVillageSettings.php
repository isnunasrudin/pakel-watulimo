<?php

namespace App\Filament\Resources\VillageSettings\Pages;

use App\Filament\Resources\VillageSettings\VillageSettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVillageSettings extends ListRecords
{
    protected static string $resource = VillageSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

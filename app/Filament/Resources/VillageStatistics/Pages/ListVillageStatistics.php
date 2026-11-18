<?php

namespace App\Filament\Resources\VillageStatistics\Pages;

use App\Filament\Resources\VillageStatistics\VillageStatisticResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVillageStatistics extends ListRecords
{
    protected static string $resource = VillageStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

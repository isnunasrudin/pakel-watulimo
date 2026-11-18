<?php

namespace App\Filament\Resources\VillageStatistics\Pages;

use App\Filament\Resources\VillageStatistics\VillageStatisticResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVillageStatistic extends EditRecord
{
    protected static string $resource = VillageStatisticResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

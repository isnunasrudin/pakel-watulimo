<?php

namespace App\Filament\Resources\VillagePillars\Pages;

use App\Filament\Resources\VillagePillars\VillagePillarResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVillagePillars extends ListRecords
{
    protected static string $resource = VillagePillarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

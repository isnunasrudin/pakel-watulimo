<?php

namespace App\Filament\Resources\VillagePillars\Pages;

use App\Filament\Resources\VillagePillars\VillagePillarResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVillagePillar extends EditRecord
{
    protected static string $resource = VillagePillarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

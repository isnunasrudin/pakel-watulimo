<?php

namespace App\Filament\Resources\ProgramLinks\Pages;

use App\Filament\Resources\ProgramLinks\ProgramLinkResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProgramLinks extends ListRecords
{
    protected static string $resource = ProgramLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

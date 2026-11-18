<?php

namespace App\Filament\Resources\ProgramLinks\Pages;

use App\Filament\Resources\ProgramLinks\ProgramLinkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProgramLink extends EditRecord
{
    protected static string $resource = ProgramLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

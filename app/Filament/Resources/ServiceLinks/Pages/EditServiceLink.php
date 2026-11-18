<?php

namespace App\Filament\Resources\ServiceLinks\Pages;

use App\Filament\Resources\ServiceLinks\ServiceLinkResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceLink extends EditRecord
{
    protected static string $resource = ServiceLinkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}

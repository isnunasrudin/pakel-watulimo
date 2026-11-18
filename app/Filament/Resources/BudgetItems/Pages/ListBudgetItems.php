<?php

namespace App\Filament\Resources\BudgetItems\Pages;

use App\Filament\Resources\BudgetItems\BudgetItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBudgetItems extends ListRecords
{
    protected static string $resource = BudgetItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

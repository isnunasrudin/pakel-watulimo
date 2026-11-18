<?php

namespace App\Filament\Resources\BudgetItems\Pages;

use App\Filament\Resources\BudgetItems\BudgetItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBudgetItem extends CreateRecord
{
    protected static string $resource = BudgetItemResource::class;
}

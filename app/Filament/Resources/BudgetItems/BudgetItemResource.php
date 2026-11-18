<?php

namespace App\Filament\Resources\BudgetItems;

use App\Filament\Resources\BudgetItems\Pages\CreateBudgetItem;
use App\Filament\Resources\BudgetItems\Pages\EditBudgetItem;
use App\Filament\Resources\BudgetItems\Pages\ListBudgetItems;
use App\Filament\Resources\BudgetItems\Schemas\BudgetItemForm;
use App\Filament\Resources\BudgetItems\Tables\BudgetItemsTable;
use App\Models\BudgetItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BudgetItemResource extends Resource
{
    protected static ?string $model = BudgetItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'APBDes';

    protected static ?string $modelLabel = 'Item APBDes';

    protected static ?string $pluralModelLabel = 'APBDes';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return BudgetItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BudgetItemsTable::configure($table);
    }

    /**
     * @return array<class-string>
     */
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBudgetItems::route('/'),
            'create' => CreateBudgetItem::route('/create'),
            'edit' => EditBudgetItem::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\HeaderMenuItems;

use App\Filament\Resources\HeaderMenuItems\Pages\CreateHeaderMenuItem;
use App\Filament\Resources\HeaderMenuItems\Pages\EditHeaderMenuItem;
use App\Filament\Resources\HeaderMenuItems\Pages\ListHeaderMenuItems;
use App\Filament\Resources\HeaderMenuItems\Schemas\HeaderMenuItemForm;
use App\Filament\Resources\HeaderMenuItems\Tables\HeaderMenuItemsTable;
use App\Models\HeaderMenuItem;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HeaderMenuItemResource extends Resource
{
    protected static ?string $model = HeaderMenuItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Menu Header';

    protected static ?string $modelLabel = 'Menu Header';

    protected static ?string $pluralModelLabel = 'Menu Header';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return HeaderMenuItemForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HeaderMenuItemsTable::configure($table);
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
            'index' => ListHeaderMenuItems::route('/'),
            'create' => CreateHeaderMenuItem::route('/create'),
            'edit' => EditHeaderMenuItem::route('/{record}/edit'),
        ];
    }
}

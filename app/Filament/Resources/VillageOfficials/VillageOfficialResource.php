<?php

namespace App\Filament\Resources\VillageOfficials;

use App\Filament\Resources\VillageOfficials\Pages\CreateVillageOfficial;
use App\Filament\Resources\VillageOfficials\Pages\EditVillageOfficial;
use App\Filament\Resources\VillageOfficials\Pages\ListVillageOfficials;
use App\Filament\Resources\VillageOfficials\Schemas\VillageOfficialForm;
use App\Filament\Resources\VillageOfficials\Tables\VillageOfficialsTable;
use App\Models\VillageOfficial;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class VillageOfficialResource extends Resource
{
    protected static ?string $model = VillageOfficial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Profil & Pemerintahan';

    protected static ?string $navigationLabel = 'Perangkat Desa';

    protected static ?string $modelLabel = 'Perangkat Desa';

    protected static ?string $pluralModelLabel = 'Perangkat Desa';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VillageOfficialForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VillageOfficialsTable::configure($table);
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
            'index' => ListVillageOfficials::route('/'),
            'create' => CreateVillageOfficial::route('/create'),
            'edit' => EditVillageOfficial::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->ordered();
    }
}

<?php

namespace App\Filament\Resources\VillagePillars;

use App\Filament\Resources\VillagePillars\Pages\CreateVillagePillar;
use App\Filament\Resources\VillagePillars\Pages\EditVillagePillar;
use App\Filament\Resources\VillagePillars\Pages\ListVillagePillars;
use App\Filament\Resources\VillagePillars\Schemas\VillagePillarForm;
use App\Filament\Resources\VillagePillars\Tables\VillagePillarsTable;
use App\Models\VillagePillar;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class VillagePillarResource extends Resource
{
    protected static ?string $model = VillagePillar::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static string|UnitEnum|null $navigationGroup = 'Profil & Pemerintahan';

    protected static ?string $navigationLabel = 'Pilar Desa';

    protected static ?string $modelLabel = 'Pilar Desa';

    protected static ?string $pluralModelLabel = 'Pilar Desa';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VillagePillarForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VillagePillarsTable::configure($table);
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
            'index' => ListVillagePillars::route('/'),
            'create' => CreateVillagePillar::route('/create'),
            'edit' => EditVillagePillar::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->ordered();
    }
}

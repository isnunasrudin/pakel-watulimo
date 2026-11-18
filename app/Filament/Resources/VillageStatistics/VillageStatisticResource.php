<?php

namespace App\Filament\Resources\VillageStatistics;

use App\Filament\Resources\VillageStatistics\Pages\CreateVillageStatistic;
use App\Filament\Resources\VillageStatistics\Pages\EditVillageStatistic;
use App\Filament\Resources\VillageStatistics\Pages\ListVillageStatistics;
use App\Filament\Resources\VillageStatistics\Schemas\VillageStatisticForm;
use App\Filament\Resources\VillageStatistics\Tables\VillageStatisticsTable;
use App\Models\VillageStatistic;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class VillageStatisticResource extends Resource
{
    protected static ?string $model = VillageStatistic::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static string|UnitEnum|null $navigationGroup = 'Profil & Pemerintahan';

    protected static ?string $navigationLabel = 'Statistik Desa';

    protected static ?string $modelLabel = 'Statistik Desa';

    protected static ?string $pluralModelLabel = 'Statistik Desa';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return VillageStatisticForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VillageStatisticsTable::configure($table);
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
            'index' => ListVillageStatistics::route('/'),
            'create' => CreateVillageStatistic::route('/create'),
            'edit' => EditVillageStatistic::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->ordered();
    }
}

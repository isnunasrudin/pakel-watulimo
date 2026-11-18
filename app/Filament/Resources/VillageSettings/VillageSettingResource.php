<?php

namespace App\Filament\Resources\VillageSettings;

use App\Filament\Resources\VillageSettings\Pages\CreateVillageSetting;
use App\Filament\Resources\VillageSettings\Pages\EditVillageSetting;
use App\Filament\Resources\VillageSettings\Pages\ListVillageSettings;
use App\Filament\Resources\VillageSettings\Schemas\VillageSettingForm;
use App\Filament\Resources\VillageSettings\Tables\VillageSettingsTable;
use App\Models\VillageSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class VillageSettingResource extends Resource
{
    protected static ?string $model = VillageSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Profil Desa';

    protected static ?string $modelLabel = 'Profil Desa';

    protected static ?string $pluralModelLabel = 'Profil Desa';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'village_name';

    public static function form(Schema $schema): Schema
    {
        return VillageSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VillageSettingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVillageSettings::route('/'),
            'create' => CreateVillageSetting::route('/create'),
            'edit' => EditVillageSetting::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return ! VillageSetting::query()->exists();
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}

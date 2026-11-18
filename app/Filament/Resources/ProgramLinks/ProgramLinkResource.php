<?php

namespace App\Filament\Resources\ProgramLinks;

use App\Filament\Resources\ProgramLinks\Pages\CreateProgramLink;
use App\Filament\Resources\ProgramLinks\Pages\EditProgramLink;
use App\Filament\Resources\ProgramLinks\Pages\ListProgramLinks;
use App\Filament\Resources\ProgramLinks\Schemas\ProgramLinkForm;
use App\Filament\Resources\ProgramLinks\Tables\ProgramLinksTable;
use App\Models\ProgramLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ProgramLinkResource extends Resource
{
    protected static ?string $model = ProgramLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLightBulb;

    protected static string|UnitEnum|null $navigationGroup = 'Profil & Pemerintahan';

    protected static ?string $navigationLabel = 'Sinergi Program';

    protected static ?string $modelLabel = 'Sinergi Program';

    protected static ?string $pluralModelLabel = 'Sinergi Program';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ProgramLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProgramLinksTable::configure($table);
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
            'index' => ListProgramLinks::route('/'),
            'create' => CreateProgramLink::route('/create'),
            'edit' => EditProgramLink::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->ordered();
    }
}

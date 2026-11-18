<?php

namespace App\Filament\Resources\ServiceLinks;

use App\Filament\Resources\ServiceLinks\Pages\CreateServiceLink;
use App\Filament\Resources\ServiceLinks\Pages\EditServiceLink;
use App\Filament\Resources\ServiceLinks\Pages\ListServiceLinks;
use App\Filament\Resources\ServiceLinks\Schemas\ServiceLinkForm;
use App\Filament\Resources\ServiceLinks\Tables\ServiceLinksTable;
use App\Models\ServiceLink;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ServiceLinkResource extends Resource
{
    protected static ?string $model = ServiceLink::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = 'Profil & Pemerintahan';

    protected static ?string $navigationLabel = 'Tautan Layanan';

    protected static ?string $modelLabel = 'Tautan Layanan';

    protected static ?string $pluralModelLabel = 'Tautan Layanan';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ServiceLinkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceLinksTable::configure($table);
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
            'index' => ListServiceLinks::route('/'),
            'create' => CreateServiceLink::route('/create'),
            'edit' => EditServiceLink::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->ordered();
    }
}

<?php

namespace App\Filament\Resources\HomeSlides;

use App\Filament\Resources\HomeSlides\Pages\CreateHomeSlide;
use App\Filament\Resources\HomeSlides\Pages\EditHomeSlide;
use App\Filament\Resources\HomeSlides\Pages\ListHomeSlides;
use App\Filament\Resources\HomeSlides\Schemas\HomeSlideForm;
use App\Filament\Resources\HomeSlides\Tables\HomeSlidesTable;
use App\Models\HomeSlide;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class HomeSlideResource extends Resource
{
    protected static ?string $model = HomeSlide::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Slide Beranda';

    protected static ?string $modelLabel = 'Slide Beranda';

    protected static ?string $pluralModelLabel = 'Slide Beranda';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return HomeSlideForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return HomeSlidesTable::configure($table);
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
            'index' => ListHomeSlides::route('/'),
            'create' => CreateHomeSlide::route('/create'),
            'edit' => EditHomeSlide::route('/{record}/edit'),
        ];
    }
}

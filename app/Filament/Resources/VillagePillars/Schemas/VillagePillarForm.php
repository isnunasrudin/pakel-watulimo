<?php

namespace App\Filament\Resources\VillagePillars\Schemas;

use App\Filament\Forms\Components\WebpFileUpload;
use App\Services\ImageBackgroundPalette;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class VillagePillarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('position')
                    ->label('Jabatan')
                    ->required()
                    ->maxLength(255),
                WebpFileUpload::make('photo')
                    ->label('Foto pilar')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(4096)
                    ->disk('public')
                    ->directory('village/pillars')
                    ->visibility('public')
                    ->webpMaxWidth(1200)
                    ->imageEditor()
                    ->helperText('JPEG, PNG, atau WebP. Rasio asli foto dipertahankan; crop hanya jika dilakukan manual. Otomatis dioptimalkan dan dikonversi ke WebP.')
                    ->columnSpanFull(),
                Select::make('background_color')
                    ->label('Warna latar foto')
                    ->options(ImageBackgroundPalette::htmlOptions())
                    ->allowHtml()
                    ->default(ImageBackgroundPalette::DEFAULT)
                    ->required()
                    ->helperText('Dipakai di balik area transparan foto.'),
                TextInput::make('phone')
                    ->label('Nomor telepon')
                    ->tel()
                    ->maxLength(30),
                TextInput::make('order')
                    ->label('Urutan')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ])->columns(2);
    }
}

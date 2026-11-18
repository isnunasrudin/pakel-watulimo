<?php

namespace App\Filament\Resources\ProgramLinks\Schemas;

use App\Filament\Forms\Components\WebpMediaLibraryFileUpload;
use App\Rules\SafeUrl;
use App\Services\ImageBackgroundPalette;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProgramLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                WebpMediaLibraryFileUpload::make('image')
                    ->label('Gambar sinergi program')
                    ->collection('image')
                    ->disk('public')
                    ->visibility('public')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(4096)
                    ->imageEditor()
                    ->webpMaxWidth(1600)
                    ->required()
                    ->helperText('Rasio asli gambar dipertahankan. Klik ikon pensil untuk crop, putar, zoom, atau atur posisi bila diperlukan. Otomatis dioptimalkan dan dikonversi ke WebP. Maksimal 4 MB.')
                    ->columnSpanFull(),
                Select::make('background_color')
                    ->label('Warna latar gambar')
                    ->options(ImageBackgroundPalette::htmlOptions())
                    ->allowHtml()
                    ->default(ImageBackgroundPalette::DEFAULT)
                    ->required()
                    ->helperText('Dipakai di balik area transparan gambar.'),
                TextInput::make('title')
                    ->label('Nama sinergi program (internal)')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Tidak tampil di beranda; dipakai untuk alt text gambar dan identifikasi di panel.'),
                TextInput::make('url')
                    ->label('Tautan gambar')
                    ->required()
                    ->maxLength(2048)
                    ->rules([
                        new SafeUrl(['http', 'https', 'mailto', 'tel'], message: 'Masukkan URL HTTP(S), mailto, tel, atau path internal yang valid.'),
                    ]),
                TextInput::make('order')
                    ->label('Urutan')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ])->columns(2);
    }
}

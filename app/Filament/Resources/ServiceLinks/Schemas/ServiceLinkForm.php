<?php

namespace App\Filament\Resources\ServiceLinks\Schemas;

use App\Rules\SafeUrl;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Nama layanan/tautan')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi singkat')
                    ->rows(2)
                    ->maxLength(500),
                Select::make('icon')
                    ->label('Ikon')
                    ->options([
                        'fas fa-id-card' => 'Administrasi',
                        'fas fa-file-alt' => 'Dokumen',
                        'fas fa-phone' => 'Kontak',
                        'fas fa-map-marker-alt' => 'Lokasi',
                        'fas fa-link' => 'Tautan',
                        'fas fa-building' => 'Pemerintahan',
                    ])
                    ->searchable(),
                TextInput::make('url')
                    ->label('Tautan')
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

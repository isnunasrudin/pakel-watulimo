<?php

namespace App\Filament\Resources\SocialLinks\Schemas;

use App\Rules\SafeUrl;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SocialLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('platform')
                    ->label('Platform')
                    ->options([
                        'Facebook' => 'Facebook',
                        'Instagram' => 'Instagram',
                        'YouTube' => 'YouTube',
                        'TikTok' => 'TikTok',
                        'X' => 'X',
                        'WhatsApp' => 'WhatsApp',
                        'Website' => 'Website',
                    ])
                    ->required(),
                TextInput::make('url')
                    ->label('URL')
                    ->required()
                    ->maxLength(2048)
                    ->rules([
                        new SafeUrl(['http', 'https'], absolute: true, message: 'Masukkan URL HTTP(S) yang valid.'),
                    ]),
                Select::make('icon')
                    ->label('Ikon')
                    ->options([
                        'fab fa-facebook' => 'Facebook',
                        'fab fa-instagram' => 'Instagram',
                        'fab fa-youtube' => 'YouTube',
                        'fab fa-tiktok' => 'TikTok',
                        'fab fa-x-twitter' => 'X',
                        'fab fa-whatsapp' => 'WhatsApp',
                        'fas fa-globe' => 'Website',
                    ])
                    ->required(),
                TextInput::make('order')
                    ->label('Urutan')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ])->columns(2);
    }
}

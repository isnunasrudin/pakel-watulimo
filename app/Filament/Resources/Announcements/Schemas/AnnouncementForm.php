<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                Textarea::make('body')
                    ->label('Isi pengumuman')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
                Select::make('level')
                    ->label('Tingkat')
                    ->options([
                        'info' => 'Informasi',
                        'warning' => 'Peringatan',
                        'urgent' => 'Mendesak',
                    ])
                    ->required()
                    ->default('info'),
                Toggle::make('is_active')
                    ->label('Tampilkan di website')
                    ->default(true),
                TextInput::make('link_label')
                    ->label('Label tautan')
                    ->maxLength(255),
                TextInput::make('link_url')
                    ->label('URL tautan')
                    ->url()
                    ->maxLength(255),
                DateTimePicker::make('starts_at')
                    ->label('Mulai tampil')
                    ->seconds(false),
                DateTimePicker::make('ends_at')
                    ->label('Berhenti tampil')
                    ->seconds(false)
                    ->after('starts_at'),
            ]);
    }
}

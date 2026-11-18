<?php

namespace App\Filament\Resources\Agendas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AgendaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Agenda Desa')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul agenda')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, callable $set): void {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug((string) $state));
                                }
                            })
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Dipakai pada URL agenda.'),
                        TextInput::make('location')
                            ->label('Lokasi')
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(4)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                        DateTimePicker::make('starts_at')
                            ->label('Mulai')
                            ->required()
                            ->seconds(false)
                            ->default(now()),
                        DateTimePicker::make('ends_at')
                            ->label('Selesai')
                            ->seconds(false)
                            ->after('starts_at'),
                        Toggle::make('is_published')
                            ->label('Tampilkan di website')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

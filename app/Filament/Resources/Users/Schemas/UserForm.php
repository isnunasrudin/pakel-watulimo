<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Password;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Akun pengguna')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->autocomplete('new-password')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->nullable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->rule(Password::min(8))
                            ->helperText('Minimal 8 karakter. Kosongkan saat edit jika tidak ingin mengubah password.'),
                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi password')
                            ->password()
                            ->revealable()
                            ->nullable()
                            ->dehydrated(false)
                            ->required(fn (callable $get): bool => filled($get('password')))
                            ->same('password'),
                        Toggle::make('is_admin')
                            ->label('Administrator')
                            ->helperText('Administrator dapat masuk ke panel dan mengelola seluruh konten.')
                            ->disabled(fn (?User $record): bool => $record?->is(auth()->user()) ?? false)
                            ->dehydrated(fn (?User $record): bool => ! ($record?->is(auth()->user()) ?? false))
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

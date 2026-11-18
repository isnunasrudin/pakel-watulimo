<?php

namespace App\Filament\Resources\HeaderMenuItems\Schemas;

use App\Models\HeaderMenuItem;
use App\Rules\SafeUrl;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class HeaderMenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Menu header')
                    ->description('Susun menu utama dan submenu. Submenu hanya bisa berada satu tingkat di bawah menu utama.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('label')
                            ->label('Label menu')
                            ->required()
                            ->maxLength(255),
                        Select::make('parent_id')
                            ->label('Menu induk')
                            ->options(fn (?HeaderMenuItem $record): array => HeaderMenuItem::query()
                                ->whereNull('parent_id')
                                ->when($record, fn (Builder $query): Builder => $query->whereKeyNot($record->getKey()))
                                ->ordered()
                                ->pluck('label', 'id')
                                ->all())
                            ->placeholder('Menu utama (tanpa induk)')
                            ->disabled(fn (?HeaderMenuItem $record): bool => $record?->children()->exists() ?? false)
                            ->helperText(fn (?HeaderMenuItem $record): string => $record?->children()->exists()
                                ? 'Menu ini memiliki submenu, jadi tidak bisa dijadikan submenu.'
                                : 'Kosongkan untuk menjadikan menu utama. Hanya menu utama yang dapat dipilih sebagai induk.'),
                        TextInput::make('url')
                            ->label('Tautan')
                            ->maxLength(2048)
                            ->placeholder('/profil atau https://contoh.com')
                            ->helperText('Boleh path internal (/profil), anchor (/#agenda), halaman kustom (/halaman/slug), atau URL lengkap. Kosongkan untuk menu induk yang hanya membuka dropdown.')
                            ->rules([
                                new SafeUrl(['http', 'https', 'mailto', 'tel'], message: 'Masukkan URL, path internal, atau anchor yang valid.'),
                            ])
                            ->columnSpanFull(),
                        TextInput::make('order')
                            ->label('Urutan')
                            ->numeric()
                            ->minValue(0)
                            ->default(fn (callable $get): int => (int) HeaderMenuItem::query()
                                ->where('parent_id', $get('parent_id'))
                                ->max('order') + 1)
                            ->helperText('Bisa juga diubah lewat tombol naik/turun di tabel.'),
                        Toggle::make('open_in_new_tab')
                            ->label('Buka di tab baru')
                            ->default(false),
                        Toggle::make('is_active')
                            ->label('Tampilkan di header')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

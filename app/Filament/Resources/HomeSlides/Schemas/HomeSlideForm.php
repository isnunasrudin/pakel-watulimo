<?php

namespace App\Filament\Resources\HomeSlides\Schemas;

use App\Filament\Forms\Components\WebpMediaLibraryFileUpload;
use App\Models\HomeSlide;
use App\Models\Photo;
use App\Rules\SafeUrl;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HomeSlideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Slide beranda')
                    ->columns(2)
                    ->schema([
                        Select::make('source_type')
                            ->label('Sumber slide')
                            ->options([
                                HomeSlide::SOURCE_MANUAL => 'Upload manual',
                                HomeSlide::SOURCE_GALLERY => 'Galeri foto',
                                HomeSlide::SOURCE_ARTICLE => 'Artikel',
                            ])
                            ->required()
                            ->default(HomeSlide::SOURCE_MANUAL)
                            ->live(),
                        TextInput::make('order')
                            ->label('Urutan')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(fn (): int => (int) (HomeSlide::query()->max('order') ?? 0) + 1)
                            ->helperText('Bisa juga diubah lewat drag-and-drop atau tombol naik/turun di tabel.'),
                        Select::make('photo_id')
                            ->label('Foto galeri')
                            ->options(fn (): array => Photo::query()
                                ->with('album')
                                ->orderBy('album_id')
                                ->orderBy('order')
                                ->get()
                                ->mapWithKeys(fn (Photo $photo): array => [
                                    $photo->id => ($photo->caption ?: 'Foto #'.$photo->id).' — '.($photo->album?->title ?? 'Tanpa album'),
                                ])
                                ->all())
                            ->searchable()
                            ->required(fn (callable $get): bool => $get('source_type') === HomeSlide::SOURCE_GALLERY)
                            ->visible(fn (callable $get): bool => $get('source_type') === HomeSlide::SOURCE_GALLERY)
                            ->columnSpanFull(),
                        Select::make('article_id')
                            ->label('Artikel')
                            ->relationship('article', 'title')
                            ->searchable()
                            ->preload()
                            ->required(fn (callable $get): bool => $get('source_type') === HomeSlide::SOURCE_ARTICLE)
                            ->visible(fn (callable $get): bool => $get('source_type') === HomeSlide::SOURCE_ARTICLE)
                            ->columnSpanFull(),
                        WebpMediaLibraryFileUpload::make('image')
                            ->label('Gambar slide')
                            ->collection('image')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(4096)
                            ->webpMaxWidth(1600)
                            ->imageEditor()
                            ->visible(fn (callable $get): bool => $get('source_type') === HomeSlide::SOURCE_MANUAL)
                            ->required(fn (string $operation, callable $get): bool => $operation === 'create' && $get('source_type') === HomeSlide::SOURCE_MANUAL)
                            ->helperText('JPEG, PNG, atau WebP. Maksimal 4 MB. Otomatis dioptimalkan dan dikonversi ke WebP.')
                            ->columnSpanFull(),
                        TextInput::make('title')
                            ->label('Judul slide')
                            ->maxLength(255)
                            ->required(fn (callable $get): bool => $get('source_type') === HomeSlide::SOURCE_MANUAL)
                            ->helperText('Kosongkan untuk memakai judul dari sumber galeri/artikel.'),
                        Textarea::make('description')
                            ->label('Deskripsi singkat')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Kosongkan untuk memakai deskripsi dari sumber galeri/artikel.')
                            ->columnSpanFull(),
                        TextInput::make('link_url')
                            ->label('Tautan slide (opsional)')
                            ->maxLength(2048)
                            ->rules([
                                new SafeUrl(['http', 'https', 'mailto', 'tel'], message: 'Masukkan URL atau path internal yang valid.'),
                            ])
                            ->helperText('Kosongkan untuk memakai link otomatis ke album galeri/artikel. Upload manual tanpa tautan tidak bisa diklik.')
                            ->columnSpanFull(),
                        Toggle::make('is_active')
                            ->label('Tampilkan di beranda')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Videos\Schemas;

use App\Filament\Forms\Components\WebpFileUpload;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul video')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $operation, ?string $state, callable $set): void {
                        if ($operation === 'create') {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Dipakai pada URL video.'),
                TextInput::make('video_url')
                    ->label('URL video')
                    ->url()
                    ->required()
                    ->maxLength(2048)
                    ->helperText('Gunakan tautan publik YouTube, Vimeo, atau halaman video lainnya.'),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->maxLength(5000)
                    ->columnSpanFull(),
                WebpFileUpload::make('thumbnail')
                    ->label('Thumbnail')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(4096)
                    ->disk('public')
                    ->directory('gallery/videos')
                    ->visibility('public')
                    ->webpMaxWidth(1600)
                    ->imageEditor()
                    ->imageEditorAspectRatioOptions(['16:9', '4:3', '1:1'])
                    ->helperText('JPEG, PNG, atau WebP. Maksimal 4 MB. Otomatis dioptimalkan dan dikonversi ke WebP.'),
                DateTimePicker::make('published_at')
                    ->label('Tanggal terbit')
                    ->seconds(false)
                    ->default(now())
                    ->helperText('Kosongkan untuk menyimpan sebagai draft.'),
            ]);
    }
}

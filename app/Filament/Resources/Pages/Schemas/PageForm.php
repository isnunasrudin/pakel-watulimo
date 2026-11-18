<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Forms\Components\WebpMediaLibraryFileUpload;
use App\Services\AttachmentFileTypes;
use App\Services\ImageOptimizer;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Kisame76\FilamentAdvancedRichEditor\Forms\Components\AdvancedRichEditor;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten halaman')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul halaman')
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
                            ->label('Slug URL')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText(fn (?string $state): string => 'Halaman dapat diakses di /halaman/'.($state ?: 'slug-halaman').'.'),
                        Textarea::make('excerpt')
                            ->label('Ringkasan')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Dipakai sebagai pengantar dan fallback meta description.')
                            ->columnSpanFull(),
                        AdvancedRichEditor::make('content')
                            ->label('Isi halaman')
                            ->required()
                            ->preset('blog')
                            ->minHeight('14rem')
                            ->maxHeight('70vh')
                            ->extraAttributes(['class' => 'article-rich-editor-resizable'])
                            ->fileAttachmentsAcceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->fileAttachmentsMaxSize(4096)
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('pages/content')
                            ->fileAttachmentsVisibility('public')
                            ->saveUploadedFileAttachmentUsing(static fn (TemporaryUploadedFile $file): string => app(ImageOptimizer::class)
                                ->storeUploadedFile($file, 'public', 'pages/content', 1920))
                            ->mediaLibraryDirectory('pages/content')
                            ->mediaLibraryTypes([
                                'video' => [],
                                'audio' => [],
                                'file' => [],
                            ])
                            ->imageEditor()
                            ->helperText('Gambar isi halaman otomatis dioptimalkan dan dikonversi ke WebP (maksimal 1920 px) di folder pages/content. Tarik handle di sudut kanan bawah untuk mengubah tinggi editor.')
                            ->columnSpanFull(),
                    ]),
                Section::make('Publikasi')
                    ->columns(2)
                    ->schema([
                        WebpMediaLibraryFileUpload::make('cover')
                            ->label('Gambar sampul')
                            ->collection('cover')
                            ->disk('public')
                            ->visibility('public')
                            ->image()
                            ->maxSize(4096)
                            ->webpMaxWidth(1600)
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions(['16:9', '4:3', '1:1'])
                            ->helperText('Opsional. Gambar otomatis dioptimalkan dan dikonversi ke WebP. Maksimal 4 MB.')
                            ->columnSpanFull(),
                        SpatieMediaLibraryFileUpload::make('attachments')
                            ->label('Lampiran file')
                            ->collection('attachments')
                            ->disk('public')
                            ->visibility('public')
                            ->multiple()
                            ->preserveFilenames()
                            ->acceptedFileTypes(AttachmentFileTypes::MIME_TYPES)
                            ->maxSize(AttachmentFileTypes::MAX_SIZE_KB)
                            ->helperText('PDF, Word, Excel, PowerPoint, TXT, atau CSV. Maksimal 10 MB per file. Tampil sebagai daftar Lampiran di bawah halaman.')
                            ->columnSpanFull(),
                        Toggle::make('is_published')
                            ->label('Terbitkan halaman')
                            ->default(false)
                            ->helperText('Halaman hanya dapat diakses publik setelah diaktifkan.')
                            ->columnSpanFull(),
                    ]),
                Section::make('SEO')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta title')
                            ->maxLength(255),
                        Textarea::make('meta_description')
                            ->label('Meta description')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ])->columns(1);
    }
}
